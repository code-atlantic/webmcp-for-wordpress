<?php
/**
 * Tests for Rate_Limiter.
 *
 * @package WebMCP
 */

namespace WebMCP\Tests;

use WebMCP\Rate_Limiter;
use WP_UnitTestCase;

/**
 * Tests for Rate_Limiter.
 */
class Test_Rate_Limiter extends WP_UnitTestCase {

	/**
	 * Rate limiter instance.
	 *
	 * @var Rate_Limiter
	 */
	private Rate_Limiter $limiter;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->limiter = new Rate_Limiter();
		// Clear the relevant cache group before each test.
		wp_cache_flush();
	}

	// -------------------------------------------------------------------------
	// check_execution()
	// -------------------------------------------------------------------------

	/**
	 * Verifies that the first request is allowed.
	 */
	public function test_execution_allows_first_request(): void {
		$this->assertTrue( $this->limiter->check_execution( 1, 'test/tool' ) );
	}

	/**
	 * Verifies execution is blocked after per-ability limit is reached.
	 */
	public function test_execution_blocks_after_per_ability_limit(): void {
		// Override rate limit to 3 for this ability.
		add_filter( 'wmcp_rate_limit', function () {
			return 3;
		} );

		for ( $i = 0; $i < 3; $i++ ) {
			$this->assertTrue( $this->limiter->check_execution( 2, 'test/limited' ) );
		}

		// 4th request should be blocked.
		$this->assertFalse( $this->limiter->check_execution( 2, 'test/limited' ) );

		remove_all_filters( 'wmcp_rate_limit' );
	}

	/**
	 * Verifies execution is blocked after global ceiling is reached.
	 */
	public function test_execution_blocks_after_global_ceiling(): void {
		// Set global ceiling to 2.
		add_filter( 'wmcp_rate_limit_global_ceiling', function () {
			return 2;
		} );

		$this->assertTrue( $this->limiter->check_execution( 3, 'test/a' ) );
		$this->assertTrue( $this->limiter->check_execution( 3, 'test/b' ) );

		// Third request should be blocked by global ceiling.
		$this->assertFalse( $this->limiter->check_execution( 3, 'test/c' ) );

		remove_all_filters( 'wmcp_rate_limit_global_ceiling' );
	}

	/**
	 * Verifies execution limits are applied per user.
	 */
	public function test_execution_limits_are_per_user(): void {
		// Override rate limit to 1.
		add_filter( 'wmcp_rate_limit', function () {
			return 1;
		} );

		$this->assertTrue( $this->limiter->check_execution( 10, 'test/tool' ) );
		$this->assertFalse( $this->limiter->check_execution( 10, 'test/tool' ) );

		// User 11 should still be allowed.
		$this->assertTrue( $this->limiter->check_execution( 11, 'test/tool' ) );

		remove_all_filters( 'wmcp_rate_limit' );
	}

	// -------------------------------------------------------------------------
	// check_discovery()
	// -------------------------------------------------------------------------

	/**
	 * Verifies that the first discovery request is allowed.
	 */
	public function test_discovery_allows_first_request(): void {
		$this->assertTrue( $this->limiter->check_discovery( '1.2.3.4' ) );
	}

	/**
	 * Verifies discovery requests are blocked after limit is reached.
	 */
	public function test_discovery_blocks_after_limit(): void {
		add_filter( 'wmcp_discovery_rate_limit', function () {
			return 2;
		} );

		$this->assertTrue( $this->limiter->check_discovery( '5.5.5.5' ) );
		$this->assertTrue( $this->limiter->check_discovery( '5.5.5.5' ) );
		$this->assertFalse( $this->limiter->check_discovery( '5.5.5.5' ) );

		remove_all_filters( 'wmcp_discovery_rate_limit' );
	}

	/**
	 * Verifies discovery limits are applied per IP.
	 */
	public function test_discovery_limits_are_per_ip(): void {
		add_filter( 'wmcp_discovery_rate_limit', function () {
			return 1;
		} );

		$this->assertTrue( $this->limiter->check_discovery( '10.0.0.1' ) );
		$this->assertFalse( $this->limiter->check_discovery( '10.0.0.1' ) );

		// Different IP should be allowed.
		$this->assertTrue( $this->limiter->check_discovery( '10.0.0.2' ) );

		remove_all_filters( 'wmcp_discovery_rate_limit' );
	}
}
