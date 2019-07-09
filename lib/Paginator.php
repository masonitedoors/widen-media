<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the Widen Media, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 */
class Paginator extends Admin {

	/**
	 * The current paged value.
	 *
	 * @var Int
	 */
	private $current_page;

	/**
	 * The max amount of items displayed per page.
	 *
	 * @var Int
	 */
	private $limit;

	/**
	 * The total number of items shown on the currnent page.
	 *
	 * @var Int
	 */
	private $current_page_items_count;

	/**
	 * The total item count.
	 *
	 * @var Int
	 */
	private $total_items_count;

	/**
	 * The page count.
	 *
	 * @var Int
	 */
	private $total_page_count;

	/**
	 * The search query.
	 *
	 * @var String
	 */
	private $query;

	/**
	 * The base url.
	 *
	 * @var String
	 */
	private $base_url;

	/**
	 * If the query has results.
	 *
	 * @var Boolean
	 */
	private $has_results;

	/**
	 * If we are on the first page.
	 *
	 * @var Boolean
	 */
	private $is_first_page;

	/**
	 * If we are on the second page.
	 *
	 * @var Boolean
	 */
	private $is_second_page;

	/**
	 * If we are on the second last page.
	 *
	 * @var Boolean
	 */
	private $is_second_last_page;

	/**
	 * If we are on the last page.
	 *
	 * @var Boolean
	 */
	private $is_last_page;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Int    $current_page             The current paged value.
	 * @param Int    $limit                    The max amount of items displayed per page.
	 * @param Int    $current_page_items_count The number of items shown for the current page.
	 * @param Int    $total_items_count        The total item count.
	 * @param String $query                    The search query.
	 */
	public function __construct( $current_page, $limit, $current_page_items_count, $total_items_count, $query ) {

		$this->current_page             = intval( $current_page );
		$this->limit                    = intval( $limit );
		$this->current_page_items_count = intval( $current_page_items_count );
		$this->total_items_count        = intval( $total_items_count );
		$this->total_page_count         = intval( ceil( $this->total_items_count / $this->limit ) );
		$this->query                    = $query;
		$this->has_results              = $this->has_results();
		$this->is_first_page            = $this->is_first_page();
		$this->is_second_page           = $this->is_second_page();
		$this->is_second_last_page      = $this->is_second_last_page();
		$this->is_last_page             = $this->is_last_page();
		$this->base_url                 = self::get_media_page_url();

	}

	/**
	 * Check if we are on the first page.
	 */
	public function is_first_page() : bool {
		return ( 1 === $this->current_page );
	}

	/**
	 * Check if we are on the second page.
	 */
	public function is_second_page() : bool {
		return ( 2 === $this->current_page );
	}

	/**
	 * Check if we are on the second last page.
	 */
	public function is_second_last_page() : bool {
		return ( ( $this->total_page_count - 1 ) === $this->current_page );
	}

	/**
	 * Check if we are on the last available page.
	 */
	public function is_last_page() : bool {
		return ( $this->total_page_count === $this->current_page );
	}

	/**
	 * If the query has results.
	 */
	public function has_results() : bool {
		return ( 0 !== $this->total_items_count );
	}

	/**
	 * Get the first page url.
	 */
	public function get_first_page_url() : string {
		$first_page_url = add_query_arg(
			[
				's'     => $this->query,
				'paged' => 1,
			],
			$this->base_url
		);

		return $first_page_url;
	}

	/**
	 * Get the previous page url.
	 */
	public function get_prev_page_url() : string {
		$prev_page_url = add_query_arg(
			[
				's'     => $this->query,
				'paged' => ( $this->current_page - 1 ),
			],
			$this->base_url
		);

		return $prev_page_url;
	}

	/**
	 * Get the next page url.
	 */
	public function get_next_page_url() : string {
		$next_page_url = add_query_arg(
			[
				's'     => $this->query,
				'paged' => ( $this->current_page + 1 ),
			],
			$this->base_url
		);

		return $next_page_url;
	}

	/**
	 * Get the last page url.
	 */
	public function get_last_page_url() : string {
		$last_page_url = add_query_arg(
			[
				's'     => $this->query,
				'paged' => $this->total_page_count,
			],
			$this->base_url
		);

		return $last_page_url;
	}

	/**
	 * Display the pagination.
	 */
	public function display() : void {

		// Pagination links.
		$first_page_url = $this->get_first_page_url();
		$prev_page_url  = $this->get_prev_page_url();
		$next_page_url  = $this->get_next_page_url();
		$last_page_url  = $this->get_last_page_url();

		$results_meta = sprintf(
			/* translators: %1$s: Total item count for current page, %2$s: Total item count, %3$s: Search query */
			__( 'Displaying %1$s of %2$s items for %3$s', 'widen-media' ),
			'<strong>' . number_format( $this->current_page_items_count ) . '</strong>',
			'<strong>' . number_format( $this->total_items_count ) . '</strong>',
			'<strong>"' . $this->query . '"</strong>',
		);

		$no_results_meta = sprintf(
			/* translators: %1$s: Search query */
			__( 'No results were found for %1$s', 'widen-media' ),
			'<strong>"' . $this->query . '"</strong>',
		);

		?>

		<div class="toolbar">

			<h2 class="screen-reader-text"><?php esc_html_e( 'Search results navigation', 'widen-media' ); ?></h2>

			<div class="tablenav-pages">

			<?php if ( $this->has_results ) : ?>

				<span class="displaying-num"><?php echo $results_meta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="pagination-links">

				<?php if ( $this->is_first_page || $this->is_second_page ) : ?>

					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>

				<?php else : ?>

					<a class="first-page button" href="<?php echo esc_url( $first_page_url ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'First page', 'widen-media' ); ?></span>
						<span aria-hidden="true">«</span>
					</a>

				<?php endif; ?>

				<?php if ( $this->is_first_page ) : ?>

					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>

				<?php else : ?>

					<a class="prev-page button" href="<?php echo esc_url( $prev_page_url ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'widen-media' ); ?></span>
						<span aria-hidden="true">‹</span>
					</a>

				<?php endif; ?>

					<span class="paging-input">
						<label class="screen-reader-text"><?php esc_html_e( 'Current page', 'widen-media' ); ?></label>
						<input class="current-page" type="text" name="paged" value="<?php echo esc_attr( $this->current_page ); ?>" size="4">
						<span class="tablenav-paging-text"> <?php esc_html_e( 'of', 'widen-media' ); ?>
							<span class="total-pages"><?php echo esc_html( number_format( $this->total_page_count ) ); ?></span>
						</span>
					</span>

				<?php if ( $this->is_last_page ) : ?>

					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>

				<?php else : ?>

					<a class="next-page button" href="<?php echo esc_url( $next_page_url ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Next page', 'widen-media' ); ?></span>
						<span aria-hidden="true">›</span>
					</a>

				<?php endif; ?>

				<?php if ( $this->is_last_page || $this->is_second_last_page ) : ?>

					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>

				<?php else : ?>

					<a class="last-page button" href="<?php echo esc_url( $last_page_url ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Last page', 'widen-media' ); ?></span>
						<span aria-hidden="true">»</span>
					</a>

				<?php endif; ?>

				</span><!-- .pagination-links -->

			<?php else : ?>

				<span class="displaying-num"><?php echo $no_results_meta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

			<?php endif; // If results. ?>

			</div><!-- .tablenav-pages -->

		</div><!-- .toolbar -->

		<?php

	}

}
