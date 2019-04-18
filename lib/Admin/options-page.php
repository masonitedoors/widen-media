<?php
/**
 * Provide the admin area view for the plugin
 *
 * @package    Widen_Media
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'WPINC' ) || die();

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<br/>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="blogname">Access Token</label>
				</th>
				<td>
					<input disabled name="access_token" type="text" id="access_token" value="<?php echo esc_attr( $this->get_access_token() ); ?>" class="regular-text">
					<?php if ( ! $this->is_access_token_defined() ) : ?>
						<p class="description">WIDEN_MEDIA_ACCESS_TOKEN must be defined within wp-config.php.</p>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>

</div>
