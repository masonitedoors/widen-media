<?php
/**
 * Results tile for a video file.
 */

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

$mp4_id          = $item['id'] ?? '';
$mp4_filename    = $item['filename'] ?? '';
$description_arr = $item['metadata']['fields']['description'] ?? [];
$description     = implode( ' ', $description_arr );
$fields_arr      = $item['metadata']['fields'] ?? [];
$fields          = wp_json_encode( $fields_arr );
$templated_url   = $item['embeds']['templated']['url'] ?? '';

$video_format      = $item['file_properties']['format'];
$video_format_type = $item['file_properties']['format_type'];

$original_url = $item['embeds']['original']['url'] ?? '';
$skeleton_url = $item['embeds']['document_thumbnail']['url'] ?? '';

// Remove query string from url.
$original_url = Util::remove_query_string( $original_url );
$skeleton_url = Util::remove_query_string( $skeleton_url );

// Check if the mp4 has already been added.
$already_added = Util::attachment_exists( $original_url );
$attachment_id = $already_added ? Util::get_attachment_id( $original_url ) : '';

// Get tile thumbnail video preview url.
$video_thumbnail = $item['embeds']['video_poster']['url'];

// Get extension of file.
$file_ext = pathinfo( $original_url );
$file_ext = $file_ext['extension'];
?>
<div class="tile video <?php echo $already_added ? 'added' : ''; ?>">
	<div class="tile__wrapper">
		<div class="extension"><?php echo esc_attr( $file_ext ); ?></div>
		<div class="tile__header" aria-hidden="true">
			<img
				class="tile__image blur-up lazyload"
				src="<?php echo esc_attr( $video_thumbnail ); ?>"
				data-src="<?php echo esc_attr( $video_thumbnail ); ?>"
				alt="<?php echo esc_attr( $description ); ?>"
			/>
		</div>
		<div class="tile__content">
			<p class="tile__title"><?php echo esc_attr( $mp4_filename ); ?></p>

			<?php if ( $already_added ) : ?>

				<div class="tile__button-wrapper">
					<a class="button-link" href="<?php echo esc_url( admin_url( "upload.php?item=$attachment_id" ) ); ?>"><?php esc_html_e( 'View In Media Library', 'widen-media' ); ?></a>
				</div>

				<?php else : ?>

				<div class="tile__button-wrapper">
					<button
						class="button add-to-library"
						data-type="video"
						data-format="<?php echo esc_attr( $video_format ); ?>"
						data-id="<?php echo esc_attr( $mp4_id ); ?>"
						data-filename="<?php echo esc_attr( $pdf_filename ); ?>"
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
