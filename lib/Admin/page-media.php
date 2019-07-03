<?php

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$query = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:disable WordPress.Security.NonceVerification.Recommended

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<br>

	<form id="widen-media" method="GET" action="<?php admin_url( 'upload.php' ); ?>">
		<input type="hidden" name="page" value="widen-media">
		<div class="search-box">
			<label class="screen-reader-text" for="widen-search-input">Search Widen:</label>
			<input type="search" id="widen-search-input" name="s" value="<?php echo esc_html( $query ); ?>">
			<input type="submit" id="widen-search-submit" class="button" value="Search Widen">
			<span id="widen-search-spinner" class="spinner"></span>
		</div>

		<?php if ( ! empty( $query ) ) : ?>

			<?php
			// Define the paginated index so we can offset the API request.
			$index  = ( isset( $_GET['paged'] ) && '0' !== $_GET['paged'] ) ? wp_unslash( $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$limit  = 25;
			$offset = ( $index - 1 ) * $limit;

			// Make our API request to Widen.
			$response = $this->widen->search( $query, $offset, $limit );

			// Get the total number of paginated pages.
			$total_item_count = $response['total_count'];
			$total_page_count = ceil( $total_item_count / $limit );

			// Get the pagination links.
			$next_page_url = add_query_arg(
				[
					'paged' => ( $index + 1 ),
				],
				self::get_media_page_url()
			);
			$last_page_url = add_query_arg(
				[
					'paged' => $total_page_count,
				],
				self::get_media_page_url()
			);
			?>

			<?php if ( $response ) : ?>

			<div class="toolbar">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Search results navigation', 'widen-media' ); ?></h2>
				<div class="tablenav-pages">
					<span class="displaying-num"><?php echo esc_html( $total_item_count ); ?> items</span>
					<span class="pagination-links">
						<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
						<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
						<span class="paging-input">
							<label for="current-page-selector" class="screen-reader-text"><?php esc_html_e( 'Current page', 'widen-media' ); ?></label>
							<input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo esc_attr( $index ); ?>" size="2" aria-describedby="table-paging">
							<span class="tablenav-paging-text"> <?php esc_html_e( 'of', 'widen-media' ); ?>
								<span class="total-pages"><?php echo esc_html( $total_page_count ); ?></span>
							</span>
						</span>
						<a class="next-page button" href="<?php echo esc_url( $next_page_url ); ?>">
							<span class="screen-reader-text"><?php esc_html_e( 'Next page', 'widen-media' ); ?></span>
							<span aria-hidden="true">›</span>
						</a>
						<a class="last-page button" href="<?php echo esc_url( $last_page_url ); ?>">
							<span class="screen-reader-text"><?php esc_html_e( 'Last page', 'widen-media' ); ?></span>
							<span aria-hidden="true">»</span>
						</a>
					</span>
				</div>
			</div>

			<div id="search-results">

				<ul class="tiles">

				<?php foreach ( $response['items'] as $item ) : ?>

					<?php $this->get_tile( $item ); ?>

				<?php endforeach; ?>

				</ul>

				<pre><?php print_r( $response['items'] ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r ?></pre>

			<?php else : ?>

				<p><?php esc_html_e( 'No results found.', 'widen-media' ); ?></p>

			<?php endif; ?>

			</div><!-- #widen-search-results -->

		<?php endif; ?>

	</form>
</div><!-- .wrap -->
