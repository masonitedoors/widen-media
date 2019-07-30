<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$query         = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : ''; // phpcs:disable WordPress.Security.NonceVerification.Recommended
$current_page  = ( isset( $_GET['paged'] ) && '0' !== $_GET['paged'] ) ? intval( wp_unslash( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$is_collection = isset( $_GET['collection'] ) && '1' === $_GET['collection'];
$limit         = $is_collection ? 100 : 25;
$offset        = ( $current_page - 1 ) * $limit;

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<p><?php esc_html_e( 'Unsupported image filetypes (anything other than JPG, PNG, & GIF) will be automatically added to the WordPress Media Library as a PNG.', 'widen-media' ); ?></p>

	<form id="widen-media" method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

		<?php wp_nonce_field( 'search_submit', 'widen_media_nonce' ); ?>

		<input type="hidden" name="action" value="handle_search_submit" />
		<input type="hidden" name="prev_search" value="<?php echo esc_attr( $query ); ?>" />

		<div class="search-box">
			<label class="screen-reader-text" for="widen-search-input"><?php esc_html_e( 'Search Widen', 'widen-media' ); ?></label>
			<input type="search" id="widen-search-input" name="search" value="<?php echo esc_attr( $query ); ?>" />
			<div class="search-option">
				<input type="checkbox" name="collection" id="widen-search-collection" class="search-option" value="1" <?php checked( $is_collection ); ?> />
				<label for="widen-search-collection" title="<?php esc_html_e( 'Search Widen Collections', 'widen-media' ); ?>"><?php esc_html_e( 'Collection', 'widen-media' ); ?></label>
			</div>
			<input type="submit" id="widen-search-submit" class="button" value="<?php esc_html_e( 'Search Widen', 'widen-media' ); ?>" />
			<span id="widen-search-spinner" class="spinner"></span>
		</div>

		<?php if ( ! empty( $query ) ) : ?>

			<?php

			// Make our API request to Widen.
			$response = $this->widen->search_assets( $query, $offset, $limit, $is_collection );

			if ( ! is_wp_error( $response ) ) {
				// Setup pagination.
				$pagination = new Paginator(
					$current_page,
					$limit,
					count( $response['items'] ),
					$response['total_count'],
					$query,
					$is_collection
				);
			} else {
				// Display error message.
				Admin::display_notice(
					'error',
					$response->get_error_message()
				);
			}

			?>

			<div id="search-results">

			<?php if ( ! is_wp_error( $response ) ) : ?>

				<?php
				// Build JSON that we can use to store the collection.
				if ( $is_collection ) {
					$json = self::json_image_query_data( $query, $response['items'] );
					echo '<script id="widen_image_query_data" type="application/json">' . $json . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>

				<?php $pagination->display(); ?>

				<ul class="tiles">

				<?php foreach ( $response['items'] as $item ) : ?>

					<?php $this->get_tile( $item, $is_collection ); ?>

				<?php endforeach; ?>

				</ul>

			<?php endif; ?>

			</div><!-- #search-results -->

		<?php endif; ?>

	</form>
</div><!-- .wrap -->

<?php
