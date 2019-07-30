<?php
/**
 * The view for the custom submitdiv meta box for the wm_collection post type
 */

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

?>
<div id="minor-publishing">
	<div id="misc-publishing-actions">
		<div class="misc-pub-section">
			<?php esc_html_e( 'Name: ', 'widen-media' ); ?><strong><?php the_title(); ?></strong>
		</div>
		<div class="misc-pub-section">
			<?php esc_html_e( 'ID: ', 'widen-media' ); ?><strong><?php the_ID(); ?></strong>
		</div>
	</div>
</div>
<div id="major-publishing-actions">
	<?php submit_button( __( 'Sync Collection', 'widen-media' ), 'primary', 'sync_collection', false ); ?>
</div>
