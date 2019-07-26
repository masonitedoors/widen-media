<?php

declare( strict_types = 1 );

// phpcs:disable NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions

use Masonite\WP\Widen_Media;

/**
 * Returns the collection object.
 *
 * @param Int $id The collection ID.
 *
 * @return WP_Error|array
 */
function wm_get_collection( int $id ) {
	$collection_obj = get_post( $id );

	// Return WP_Error if no collection was found.
	if ( empty( $collection_obj ) ) {
		$error         = new WP_Error();
		$error_message = sprintf(
			/* translators: %1$d: The collection ID */
			__( 'No collection was found with the ID %1$d', 'widen-media' ),
			$id,
		);

		$error->add( 'collection_not_found', $error_message );

		return $error;
	}

	// Get the collection items.
	$items_str = get_post_meta( $id, 'items', true );
	$items     = json_decode( $items_str, true );

	// Build the collection we want to return array.
	$collection = [
		'id'          => $collection_obj->ID ?? null,
		'title'       => $collection_obj->post_title ?? null,
		'name'        => $collection_obj->post_name ?? null,
		'total_count' => count( $items ),
		'items'       => $items,
	];

	return $collection;
}
