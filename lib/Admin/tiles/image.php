<?php
/**
 * Results tile for an image
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$image_id       = $item['id'];
$image_filename = $item['filename'];
$original_url   = $item['embeds']['original']['url'];
$thumbnail_url  = $item['embeds']['ThumbnailPNG']['url'];
$skeleton_url   = $item['embeds']['SkeletonPNG']['url'];
$description    = implode( ' ', $item['metadata']['fields']['description'] );

if ( strpos( $original_url, '.tif' ) !== false ) {
	$original_url = $item['embeds']['OriginalPNG']['url'];
}

// Remove query string from url.
$original_url = preg_replace( '/\?.*/', '', $original_url );

// Check if the image has already been added.
$already_added = self::attachment_exists( $original_url );

?>
<div class="tile image <?php echo $already_added ? 'added' : ''; ?>">
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
			<p class="tile__title"><?php echo esc_attr( $image_filename ); ?></p>

			<?php if ( $already_added ) : ?>

				<div class="tile__button-wrapper">
					<button disabled class="button already-added"><?php esc_html_e( '✓ Added', 'widen-media' ); ?></button>
				</div>

			<?php else : ?>

				<div class="tile__button-wrapper">
					<button
						class="button add-to-library"
						data-type="image"
						data-id="<?php echo esc_attr( $image_id ); ?>"
						data-filename="<?php echo esc_attr( $image_filename ); ?>"
						data-description="<?php echo esc_attr( $description ); ?>"
						data-url="<?php echo esc_attr( $original_url ); ?>"
					><?php esc_html_e( 'Add To Media Library', 'widen-media' ); ?></button>
					<span class="spinner"></span>
				</div>

			<?php endif; ?>

		</div>
	</div>
</div>
