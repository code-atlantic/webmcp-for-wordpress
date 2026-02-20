/**
 * WebMCP Abilities — front-end imperative tool registration.
 *
 * Fetches registered WordPress Abilities from the REST API and registers
 * each one as a WebMCP tool via navigator.modelContext, making them available
 * to AI agents in Chrome 146+.
 */

interface ToolDefinition {
	name: string;
	description: string;
	inputSchema?: Record< string, unknown >;
	annotations?: ToolAnnotations;
}

interface ToolsCache {
	tools: ToolDefinition[];
	etag: string;
	expiry: number;
}

interface ToolsResponse {
	tools: ToolDefinition[];
	nonce?: string;
}

( function () {
	'use strict';

	// Guard: only run in browsers that support the WebMCP API.
	if ( typeof navigator === 'undefined' || ! ( 'modelContext' in navigator ) ) {
		return;
	}

	// Guard: wmcpBridge config must be present (injected via wp_localize_script).
	if ( typeof wmcpBridge === 'undefined' ) {
		return;
	}

	const { toolsEndpoint, executeEndpoint, nonceEndpoint } = wmcpBridge;
	let currentNonce: string = wmcpBridge.nonce;

	// -------------------------------------------------------------------------
	// LocalStorage cache for tool definitions (24h TTL).
	// Nonces are NOT cached — they're returned fresh with each /tools response.
	// -------------------------------------------------------------------------

	const CACHE_KEY = 'wmcp_tools_cache';
	const CACHE_TTL = 24 * 60 * 60 * 1000; // 24 hours in ms

	function getCachedTools(): { tools: ToolDefinition[]; etag: string } | null {
		try {
			const raw = localStorage.getItem( CACHE_KEY );
			if ( ! raw ) {
				return null;
			}

			const { tools, etag, expiry } = JSON.parse( raw ) as ToolsCache;
			if ( Date.now() > expiry ) {
				localStorage.removeItem( CACHE_KEY );
				return null;
			}
			return { tools, etag };
		} catch {
			return null;
		}
	}

	function setCachedTools( tools: ToolDefinition[], etag: string ): void {
		try {
			localStorage.setItem(
				CACHE_KEY,
				JSON.stringify( {
					tools,
					etag,
					expiry: Date.now() + CACHE_TTL,
				} )
			);
		} catch {
			// localStorage may be unavailable (private browsing, quota exceeded).
		}
	}

	// -------------------------------------------------------------------------
	// Nonce refresh — called on 403 responses.
	// -------------------------------------------------------------------------

	async function refreshNonce(): Promise< void > {
		try {
			const response = await fetch( nonceEndpoint, {
				credentials: 'same-origin',
			} );
			if ( response.ok ) {
				const data = ( await response.json() ) as { nonce?: string };
				currentNonce = data.nonce ?? currentNonce;
			}
		} catch {
			// Silently fail — the next execution attempt will hit a 403 and retry.
		}
	}

	// -------------------------------------------------------------------------
	// Fetch tools from the REST API, respecting ETag for cache validation.
	// -------------------------------------------------------------------------

	async function fetchTools(): Promise< ToolDefinition[] > {
		const cached = getCachedTools();

		// Do NOT send X-WP-Nonce on the tools request — WP core rejects an invalid
		// nonce with 403 before our permission callback runs. Cookie auth alone is
		// sufficient for the is_user_logged_in() check on the tools endpoint.
		const headers: Record< string, string > = {};

		if ( cached?.etag ) {
			headers[ 'If-None-Match' ] = `"${ cached.etag }"`;
		}

		let response: Response;
		try {
			response = await fetch( toolsEndpoint, {
				headers,
				credentials: 'same-origin',
			} );
		} catch {
			// Network error — use cached tools if available.
			return cached?.tools ?? [];
		}

		// 304 Not Modified — cached tools are still valid.
		if ( response.status === 304 && cached?.tools ) {
			return cached.tools;
		}

		if ( ! response.ok ) {
			return cached?.tools ?? [];
		}

		const data = ( await response.json() ) as ToolsResponse;
		const tools = data.tools ?? [];
		const etag = response.headers.get( 'ETag' )?.replace( /"/g, '' ) ?? '';

		// Update the current nonce from the response.
		if ( data.nonce ) {
			currentNonce = data.nonce;
		}

		setCachedTools( tools, etag );
		return tools;
	}

	// -------------------------------------------------------------------------
	// Execute a tool via the REST API.
	// Auto-retries once on 403 (expired nonce).
	// -------------------------------------------------------------------------

	async function executeTool(
		toolName: string,
		input: Record< string, unknown >,
		readOnly = false
	): Promise< McpResult > {
		const url = executeEndpoint + encodeURIComponent( toolName );

		async function doRequest( nonce: string ): Promise< Response > {
			const reqHeaders: Record< string, string > = {
				'Content-Type': 'application/json',
			};
			// Read-only tools skip the nonce — WP core would reject a stale nonce
			// with 403 before our permission check runs, and the server doesn't
			// require it for read-only tools anyway.
			if ( ! readOnly && nonce ) {
				reqHeaders[ 'X-WP-Nonce' ] = nonce;
			}
			return fetch( url, {
				method: 'POST',
				credentials: 'same-origin',
				headers: reqHeaders,
				body: JSON.stringify( input ),
			} );
		}

		let response = await doRequest( currentNonce );

		// On 403 for write tools, refresh the nonce and retry once.
		if ( ! readOnly && response.status === 403 ) {
			await refreshNonce();
			response = await doRequest( currentNonce );
		}

		if ( ! response.ok ) {
			let errorMessage = `HTTP ${ response.status }`;
			try {
				const errData = ( await response.json() ) as { message?: string };
				errorMessage = errData.message ?? errorMessage;
			} catch {
				/* non-JSON body — use status code */
			}
			throw new Error( `WebMCP Abilities: ${ errorMessage }` );
		}

		const data = ( await response.json() ) as { result: unknown };

		// Chrome's WebMCP implementation expects the MCP content-array format.
		return {
			content: [ { type: 'text', text: JSON.stringify( data.result ) } ],
		};
	}

	// -------------------------------------------------------------------------
	// Register all tools with navigator.modelContext.
	// -------------------------------------------------------------------------

	async function registerTools(): Promise< void > {
		let tools: ToolDefinition[];
		try {
			tools = await fetchTools();
		} catch {
			return;
		}

		if ( ! Array.isArray( tools ) || tools.length === 0 ) {
			return;
		}

		// Build the tool list in the format the spec requires.
		const mcpTools: McpTool[] = tools
			.filter( ( tool ) => tool.name && tool.description )
			.map( ( tool ) => {
				// Gemini rejects tool names containing '/'. Sanitize for WebMCP
				// registration while keeping the original name for the execute URL.
				const safeName = tool.name.replace( /\//g, '_' );

				const entry: McpTool = {
					name: safeName,
					description: tool.description,
					inputSchema: tool.inputSchema ?? {
						type: 'object',
						properties: {},
					},
					execute: async (
						input: Record< string, unknown >
					): Promise< McpResult > =>
						executeTool(
							tool.name,
							input,
							!! tool.annotations?.readOnlyHint
						),
				};
				if ( tool.annotations ) {
					entry.annotations = tool.annotations;
				}
				return entry;
			} );

		if ( mcpTools.length === 0 ) {
			return;
		}

		// provideContext() atomically replaces the full tool set — safer than
		// looping registerTool() which throws on duplicate names.
		navigator.modelContext!.provideContext( { tools: mcpTools } );
	}

	// -------------------------------------------------------------------------
	// Entry point.
	// -------------------------------------------------------------------------

	registerTools();
} )();
