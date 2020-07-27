<?php
/**
 * Results tile for an image
 */

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$image_id        = $item['id'] ?? '';
$image_filename  = $item['filename'] ?? '';
$templated_url   = $item['embeds']['templated']['url'] ?? '';
$description_arr = $item['metadata']['fields']['description'] ?? [];
$description     = implode( ' ', $description_arr );
$fields_arr      = $item['metadata']['fields'] ?? [];
$fields          = wp_json_encode( $fields_arr );

// Create the image URL's needed for this view.
$original_url  = Widen::create_url_from_template( $templated_url );
$thumbnail_url = Widen::create_url_from_template( $templated_url, 500, 500 );
$skeleton_url  = Widen::create_url_from_template( $templated_url, 50, 50 );

// Check if the image has already been added.
$already_added = Util::attachment_exists( $original_url );
$attachment_id = $already_added ? Util::get_attachment_id( $original_url ) : '';

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
					<a class="button-link" href="<?php echo esc_url( admin_url( "upload.php?item=$attachment_id" ) ); ?>"><?php esc_html_e( 'View In Media Library', 'widen-media' ); ?></a>
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
						data-templated-url="<?php echo esc_attr( Util::sanitize_image_url( $templated_url ) ); ?>"
						data-fields="<?php echo esc_attr( $fields ); ?>"
					><?php esc_html_e( 'Add to Media Library', 'widen-media' ); ?></button>
					<span class="spinner"></span>
				</div>

			<?php endif; ?>

		</div>
	</div>
</div>
