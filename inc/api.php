<?php

declare( strict_types = 1 );

// phpcs:disable NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions

use Masonite\WP\Widen_Media;

if ( ! function_exists( 'wm_get_collections' ) ) :

	/**
	 * Returns an arry of collection objects.
	 */
	function wm_get_collections(): array {
		$collections       = [];
		$args              = [
			'post_type' => 'wm_collection',
			'nopaging'  => true,
			'order'     => 'ASC',
			'orderby'   => 'menu_order',
		];
		$collections_query = new WP_Query( $args );

		if ( $collections_query->have_posts() ) {
			while ( $collections_query->have_posts() ) {
				$collections_query->the_post();
				$collections[] = wm_get_collection( get_the_ID() );
			}
		}

		wp_reset_postdata();

		return $collections;
	}

endif;

if ( ! function_exists( 'wm_get_collection' ) ) :

	/**
	 * Returns the collection object.
	 *
	 * @param int $collection_id The collection ID.
	 */
	function wm_get_collection( int $collection_id ): ?object {
		$collection_wp_obj = get_post( $collection_id );

		if ( empty( $collection_wp_obj ) ) {
			return null;
		}

		$collection = new stdClass();

		$collection->ID    = $collection_wp_obj->ID ?? null;
		$collection->title = $collection_wp_obj->post_title ?? null;
		$collection->name  = $collection_wp_obj->post_name ?? null;

		// Get the collection items.
		$items                   = json_decode( get_post_meta( $collection_id, 'items', true ) );
		$collection->total_count = count( $items );
		$collection->items       = $items;

		return $collection;
	}

endif;

if ( ! function_exists( 'wm_get_asset_fields' ) ) :

	/**
	 * Returns all the fields from Widen for an asset that exists within the WordPress Media Library.
	 *
	 * @param int $asset_id The asset ID.
	 */
	function wm_get_asset_fields( int $asset_id ) {
		$fields_str = get_post_meta( $asset_id, 'widen_media_fields', true );

		$fields = json_decode( $fields_str );

		return $fields;
	}

endif;

if ( ! function_exists( 'wm_get_asset_field' ) ) :

	/**
	 * Returns a single field for a Widen asset that exists within the WordPress Media Library.
	 *
	 * @param int     $asset_id The asset ID.
	 * @param string  $key      The name/key of the field. This is the same way it comes back from the Widen API..
	 * @param boolean $single   If true, returns only the first value for the specified meta key.
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

endif;
