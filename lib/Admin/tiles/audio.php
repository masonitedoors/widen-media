<?php
/**
 * Results tile for audio
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$filename     = $item['filename'];
$original_url = $item['embeds']['original']['url'];
$description  = implode( ' ', $item['metadata']['fields']['description'] );

?>
<div class="tile audio">
	<div class="tile__wrapper">
		<div class="tile__header" aria-hidden="true">
			<figure>
				<figcaption><?php echo esc_attr( $description ); ?></figcaption>
				<audio src="<?php echo esc_url( $original_url ); ?>" controls controlsList="nodownload">
					Your browser does not support the <code>audio</code> element.
				</audio>
			</figure>
		</div>
		<div class="tile__content">
			<p class="tile__title"><?php echo esc_attr( $filename ); ?></p>
		</div>
	</div>
</div>
