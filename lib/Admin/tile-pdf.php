<?php
/**
 * Results tile for a pdf
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$filename = $item['filename'];

?>
<div class="tile">
	<div class="tile__header" aria-hidden="true">
		<img
			class="tile__image blur-up lazyloaded"
			src="<?php echo esc_attr(); ?>"
			data-src=""
			alt=""
		/>
	</div>
	<div class="tile__content">
		<p class="tile__title"><?php echo esc_html( $filename ); ?></p>
	</div>
</div>
