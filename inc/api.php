<?php

declare( strict_types = 1 );

// phpcs:disable NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions

use Masonite\WP\Widen_Media;

/**
 * Returns the collection object.
 *
 * @param Int $id The collection ID.
 */
function wm_get_collection( int $id ) : ?object {
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
