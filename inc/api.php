<?php

declare( strict_types = 1 );

// phpcs:disable NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions

use Masonite\WP\Widen_Media;

/**
 * Returns the collection object.
 *
 * @param Int $collection_id The collection ID.
 */
function wm_get_collection( int $collection_id ) : ?object {
	$collection_wp_obj = get_post( $id );

	if ( empty( $collection_wp_obj ) ) {
		return null;
	}

	$collection = new stdClass();

	$collection->ID          = $collection_wp_obj->ID ?? null;
	$collection->title       = $collection_wp_obj->post_title ?? null;
	$collection->name        = $collection_wp_obj->post_name ?? null;
	$collection->total_count = count( $items );

	// Get the collection items.
	$items = json_decode( get_post_meta( $id, 'items', true ) );

	$collection->items = $items;

	return $collection;
}

/**
 * Returns all the fields from Widen for an asset that exists within the WordPress Media Library.
 *
 * @param Int $asset_id The asset ID.
 */
function wm_get_asset_fields( int $asset_id ) {
	$fields_str = get_post_meta( $asset_id, 'widen_media_fields', true );

	$fields = json_decode( $fields_str );

	return $fields;
}

/**
 * Returns a single field for a Widen asset that exists within the WordPress Media Library.
 *
 * @param Int     $asset_id The asset ID.
 * @param String  $key      The name/key of the field. This is the same way it comes back from the Widen API..
 * @param Boolean $single   If true, returns only the first value for the specified meta key.
 *
 * @uses wm_get_asset_fields() to get the fields array.
 */
function wm_get_asset_field( int $asset_id, string $key, bool $single = false ) {
	$fields = wm_get_asset_fields( $asset_id );

	$field = $fields->$key;

	if ( $single && is_array( $field ) ) {
		return $field[0];
	}

	return $field;
}
