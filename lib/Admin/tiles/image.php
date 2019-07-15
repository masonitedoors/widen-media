<?php
/**
 * Results tile for an image
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$filename      = $item['filename'];
$original_url  = $item['embeds']['original']['url'];
$thumbnail_url = $item['embeds']['ThumbnailPNG']['url'];
$skeleton_url  = $item['embeds']['SkeletonPNG']['url'];
$description   = implode( ' ', $item['metadata']['fields']['description'] );
$item_json     = wp_json_encode( $item );

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
			<button class="button add-to-library" data-item="<?php echo esc_attr( $item_json ); ?>">
				<?php esc_html_e( 'Add To Media Library', 'widen-media' ); ?>
			</button>
		</div>
	</div>
</div>
