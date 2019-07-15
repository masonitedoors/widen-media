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

$base_url = self::get_media_asset_page();
$item_url = add_query_arg(
	[
		'id' => $item['id'],
	],
	$base_url
);

?>
<div class="tile image">
	<a href="<?php echo esc_url( $item_url ); ?>" title="<?php echo esc_attr( $filename ); ?>">
		<div class="tile__header" aria-hidden="true">
			<img
				class="tile__image blur-up lazyload"
				src="<?php echo esc_url( $skeleton_url ); ?>"
				data-src="<?php echo esc_url( $thumbnail_url ); ?>"
				alt="<?php echo esc_attr( $description ); ?>"
			/>
		</div>
		<div class="tile__content">
			<p class="tile__title"><?php echo esc_attr( $item['id'] ); ?></p>
			<p class="tile__title"><?php echo esc_attr( $filename ); ?></p>
		</div>
	</a>
</div>
