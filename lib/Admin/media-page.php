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

	<br>

	<form id="widen-media" method="post" novalidate="novalidate">
		<div class="search-box">
			<label class="screen-reader-text" for="widen-search-input">Search Widen:</label>
			<input type="search" id="widen-search-input" name="swiden">
			<input type="submit" id="widen-search-submit" class="button" value="Search Widen">
			<span id="widen-search-spinner" class="spinner"></span>
		</div>
	</form>

	<br>

	<div id="widen-search-results">
		<div class="search-results-info wp-filter">
			<div id="widen-total-items"></div>
		</div>

		<ul class="tiles">
			<!-- Search results appended here  -->
		</ul>
	</div>

</div>
