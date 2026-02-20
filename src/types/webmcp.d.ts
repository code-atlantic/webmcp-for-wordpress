/**
 * WebMCP type declarations for navigator.modelContext and plugin globals.
 */

interface McpResult {
	content: Array< { type: string; text: string } >;
}

interface ToolAnnotations {
	readOnlyHint?: boolean;
	[ key: string ]: unknown;
}

interface McpTool {
	name: string;
	description: string;
	inputSchema: Record< string, unknown >;
	annotations?: ToolAnnotations;
	execute: ( input: Record< string, unknown > ) => Promise< McpResult >;
}

interface ProvideContextOptions {
	tools: McpTool[];
}

interface ModelContext {
	provideContext( context: ProvideContextOptions ): void;
	registerTool( tool: McpTool ): void;
}

interface WmcpBridgeConfig {
	toolsEndpoint: string;
	executeEndpoint: string;
	nonceEndpoint: string;
	nonce: string;
}

interface Navigator {
	modelContext?: ModelContext;
}

// eslint-disable-next-line no-var
declare var wmcpBridge: WmcpBridgeConfig | undefined;
