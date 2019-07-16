<?php
/**
 * Results tile for an image
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$item_id       = $item['id'];
$filename      = $item['filename'];
$original_url  = $item['embeds']['original']['url'];
$thumbnail_url = $item['embeds']['ThumbnailPNG']['url'];
$skeleton_url  = $item['embeds']['SkeletonPNG']['url'];
$description   = implode( ' ', $item['metadata']['fields']['description'] );
$url           = $original_url;

?>
<div class="tile image">
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
			<div class="tile__button-wrapper">
				<button
					class="button add-to-library"
					data-type="image"
					data-id="<?php echo esc_attr( $item_id ); ?>"
					data-filename="<?php echo esc_attr( $filename ); ?>"
					data-description="<?php echo esc_attr( $description ); ?>"
					data-url="<?php echo esc_attr( $url ); ?>"
				><?php esc_html_e( 'Add To Media Library', 'widen-media' ); ?></button>
				<span class="spinner"></span>
			</div>
		</div>
	</div>
</div>
