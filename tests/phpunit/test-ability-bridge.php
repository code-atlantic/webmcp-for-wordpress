<?php
/**
 * Tests for Ability_Bridge.
 *
 * @package WebMCP_Bridge
 */

namespace WebMCP_Bridge\Tests;

use WebMCP_Bridge\Ability_Bridge;
use WebMCP_Bridge\Settings;
use WP_UnitTestCase;

class Test_Ability_Bridge extends WP_UnitTestCase {

	private Ability_Bridge $bridge;
	private Settings $settings;

	public function setUp(): void {
		parent::setUp();
		$this->settings = new Settings();
		$this->bridge   = new Ability_Bridge( $this->settings );

		// Enable the plugin for tests.
		update_option( Settings::OPTION_ENABLED, true );
	}

	public function tearDown(): void {
		delete_option( Settings::OPTION_ENABLED );
		delete_option( Settings::OPTION_EXPOSED_TOOLS );
		parent::tearDown();
	}

	// -------------------------------------------------------------------------
	// convert() — visibility
	// -------------------------------------------------------------------------

	public function test_convert_returns_null_for_private_ability(): void {
		$ability = array(
			'label'               => 'Private Tool',
			'description'         => 'Should be hidden',
			'wmcp_visibility'     => 'private',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$this->assertNull( $this->bridge->convert( 'test/private', $ability ) );
	}

	public function test_convert_returns_tool_for_public_ability(): void {
		$ability = array(
			'label'               => 'Public Tool',
			'description'         => 'Should be visible',
			'wmcp_visibility'     => 'public',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$tool = $this->bridge->convert( 'test/public', $ability );

		$this->assertIsArray( $tool );
		$this->assertSame( 'test/public', $tool['name'] );
		$this->assertSame( 'Should be visible', $tool['description'] );
	}

	public function test_convert_defaults_to_public_when_visibility_not_set(): void {
		$ability = array(
			'label'               => 'No Visibility Key',
			'description'         => 'Missing wmcp_visibility',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$tool = $this->bridge->convert( 'test/no-vis', $ability );
		$this->assertIsArray( $tool );
	}

	// -------------------------------------------------------------------------
	// convert() — permission_callback
	// -------------------------------------------------------------------------

	public function test_convert_returns_null_when_permission_denied(): void {
		$ability = array(
			'label'               => 'Locked Tool',
			'description'         => 'Admin only',
			'permission_callback' => '__return_false',
			'execute_callback'    => '__return_null',
		);

		$this->assertNull( $this->bridge->convert( 'test/locked', $ability ) );
	}

	// -------------------------------------------------------------------------
	// convert() — admin exposed list
	// -------------------------------------------------------------------------

	public function test_convert_returns_null_when_not_in_exposed_list(): void {
		update_option( Settings::OPTION_EXPOSED_TOOLS, array( 'other/tool' ) );

		$ability = array(
			'label'               => 'Not Listed',
			'description'         => 'Not in exposed list',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$this->assertNull( $this->bridge->convert( 'test/not-listed', $ability ) );
	}

	public function test_convert_returns_tool_when_in_exposed_list(): void {
		update_option( Settings::OPTION_EXPOSED_TOOLS, array( 'test/listed' ) );

		$ability = array(
			'label'               => 'Listed Tool',
			'description'         => 'In exposed list',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$tool = $this->bridge->convert( 'test/listed', $ability );
		$this->assertIsArray( $tool );
	}

	// -------------------------------------------------------------------------
	// convert() — description sanitization
	// -------------------------------------------------------------------------

	public function test_convert_strips_html_from_description(): void {
		$ability = array(
			'label'               => 'Safe Tool',
			'description'         => '<script>alert("xss")</script>Safe description',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$tool = $this->bridge->convert( 'test/safe', $ability );
		$this->assertIsArray( $tool );
		$this->assertStringNotContainsString( '<script>', $tool['description'] );
		$this->assertStringContainsString( 'Safe description', $tool['description'] );
	}

	// -------------------------------------------------------------------------
	// validate_schema()
	// -------------------------------------------------------------------------

	public function test_validate_schema_returns_empty_object_for_empty_input(): void {
		$schema = $this->bridge->validate_schema( array() );
		$this->assertSame( array( 'type' => 'object', 'properties' => array() ), $schema );
	}

	public function test_validate_schema_rejects_excessive_depth(): void {
		$deep = array( 'a' => array( 'b' => array( 'c' => array( 'd' => array( 'e' => array( 'f' => 'too deep' ) ) ) ) ) );
		$schema = $this->bridge->validate_schema( $deep );
		$this->assertSame( array( 'type' => 'object', 'properties' => array() ), $schema );
	}

	public function test_validate_schema_rejects_dollar_ref(): void {
		$schema_with_ref = array(
			'type'       => 'object',
			'properties' => array(
				'id' => array( '$ref' => '#/definitions/Id' ),
			),
		);
		$schema = $this->bridge->validate_schema( $schema_with_ref );
		$this->assertSame( array( 'type' => 'object', 'properties' => array() ), $schema );
	}

	public function test_validate_schema_passes_valid_schema(): void {
		$valid = array(
			'type'       => 'object',
			'properties' => array(
				'query' => array( 'type' => 'string' ),
				'limit' => array( 'type' => 'integer' ),
			),
			'required'   => array( 'query' ),
		);
		$schema = $this->bridge->validate_schema( $valid );
		$this->assertSame( $valid, $schema );
	}

	// -------------------------------------------------------------------------
	// wmcp_expose_ability filter
	// -------------------------------------------------------------------------

	public function test_wmcp_expose_ability_filter_can_hide_tool(): void {
		add_filter( 'wmcp_expose_ability', '__return_false' );

		$ability = array(
			'label'               => 'Filtered Tool',
			'description'         => 'Hidden by filter',
			'permission_callback' => '__return_true',
			'execute_callback'    => '__return_null',
		);

		$result = $this->bridge->convert( 'test/filtered', $ability );

		remove_filter( 'wmcp_expose_ability', '__return_false' );

		$this->assertNull( $result );
	}
}
