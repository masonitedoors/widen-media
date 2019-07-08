<?php

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$query        = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:disable WordPress.Security.NonceVerification.Recommended
$current_page = ( isset( $_GET['paged'] ) && '0' !== $_GET['paged'] ) ? intval( wp_unslash( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$limit        = 25;
$offset       = ( $current_page - 1 ) * $limit;

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<br>

	<form id="widen-media" method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

		<input type="hidden" name="action" value="handle_search_submit" />
		<input type="hidden" name="prev_s" value="<?php echo esc_attr( $query ); ?>" />

		<?php wp_nonce_field( 'search_submit', 'widen_media_nonce' ); ?>

		<div class="search-box">
			<label class="screen-reader-text" for="widen-search-input">Search Widen:</label>
			<input type="search" id="widen-search-input" name="s" value="<?php echo esc_attr( $query ); ?>" />
			<input type="submit" id="widen-search-submit" class="button" value="Search Widen" />
			<span id="widen-search-spinner" class="spinner"></span>
		</div>

		<?php if ( ! empty( $query ) ) : ?>

			<?php

			// Make our API request to Widen.
			$response = $this->widen->search( $query, $offset, $limit );

			// Setup our pagination.
			$pagination = new Paginator(
				$current_page,
				$limit,
				$response['total_count'],
				$query
			);

			?>

			<div id="search-results">

			<?php if ( $response ) : ?>

				<?php $pagination->display(); ?>

				<ul class="tiles">

				<?php foreach ( $response['items'] as $item ) : ?>

					<?php $this->get_tile( $item ); ?>

				<?php endforeach; ?>

				</ul>

				<pre><?php print_r( $response['items'] ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r ?></pre>

			<?php else : ?>

				<p><?php esc_html_e( 'No results found.', 'widen-media' ); ?></p>

			<?php endif; ?>

			</div><!-- #search-results -->

		<?php endif; ?>

	</form>
</div><!-- .wrap -->
