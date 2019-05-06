<?php
/**
 * Tests for the widen-media file
 *
 * @package Widen_Media
 */

declare( strict_types = 1 );

namespace Masonite\Widen_Media;

/**
 * Class Test_Widen_Media.
 */
class Test_Widen_Media extends \WP_UnitTestCase {

	/**
	 * Test version_check().
	 */
	public function test_version_check() {
		$this->assertEquals( 10, has_action( 'admin_init', __NAMESPACE__ . '\version_check' ) );
	}

	/**
	 * Test load_widen_media().
	 */
	public function test_load_widen_media() {
		do_action( 'plugins_loaded' );
		$this->assertEquals( 10, has_action( 'plugins_loaded', __NAMESPACE__ . '\load_widen_media' ) );
	}
}
