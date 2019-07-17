<?php
/**
 * Results tile for a pdf
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$filename      = $item['filename'];
$original_url  = $item['embeds']['original']['url'];
$thumbnail_url = $item['embeds']['document_thumbnail']['url'];
$skeleton_url  = $item['embeds']['document_thumbnail']['url'];
$description   = implode( ' ', $item['metadata']['fields']['description'] );

?>
<div class="tile pdf">
	<div class="tile__wrapper">
		<div class="tile__header" aria-hidden="true">
			<img
				class="tile__image blur-up lazyload"
				src="<?php echo esc_url( $skeleton_url ); ?>"
				data-src="<?php echo esc_url( $thumbnail_url ); ?>"
				alt="<?php echo esc_attr( $description ); ?>"
			/>
		</div>
		<div class="tile__content">
			<p class="tile__title"><?php echo esc_attr( $filename ); ?></p>
		</div>
	</div>
</div>
