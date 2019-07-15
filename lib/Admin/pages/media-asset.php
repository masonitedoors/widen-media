<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$asset_id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : ''; // phpcs:disable WordPress.Security.NonceVerification.Recommended
$response = $this->widen->get_asset( $asset_id );

if ( is_wp_error( $response ) ) {
	// Display error message.
	Admin::display_notice(
		'error',
		$response->get_error_message()
	);
}

$title = $response['filename'];

?>
<pre><?php print_r( $response ); ?></pre>

<div class="wrap">

	<h1><?php echo esc_html( $title ); ?></h1>

	<br>

	<form id="widen-media" method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

	</form>
</div><!-- .wrap -->
