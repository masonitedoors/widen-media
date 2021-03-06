# Widen Media

[![Widen Media on Packagist](https://img.shields.io/packagist/v/masonitedoors/widen-media.svg?style=flat)](https://packagist.org/packages/masonitedoors/widen-media)

> Search and add Widen digital assets to your WordPress media library.

This WordPress plugin lets users search for images in [Widen](https://www.widen.com/) and add them to their site's media library. This plugin does not download media from Widen but rather creates a new WordPress media attachment that refers to the Widen asset URL.
Some additional Widen metadata is captured and saved to the database on the "Add to Media Library" action. This plugin does not currently support syncing of meta data between Widen & WordPress.

## Installation

You can either [download a release zip](https://github.com/masonitedoors/widen-media/releases) and install via the WordPress plugin installer or via composer.

### Composer

```shell
composer require masonitedoors/widen-media
```

## Configuration

### WordPress

This plugin uses [V2 of the Widen API](https://widenv2.docs.apiary.io/). You will need to define your Widen API access token in `wp-config.php`.

```php
define( 'WIDEN_MEDIA_ACCESS_TOKEN', 'my-widen-api-token' );
```

## Widen Metadata

Some additional Widen metadata is captured and saved to the database on the `Add to Media Library` action. This plugin does not currently support syncing of meta data between Widen & WordPress.

## Collections

This plugin saves Widen collections under the post type `wm_collection` in order to provide a way for other themes and plugins to have access to this data without having to directly interact with Widen's API.

Within the main search page under "Add New", users can toggle "collection" when searching Widen. This will return only results that match that collection name.

When searching for a collection, a _Save Collection_ button will be displayed. This button saves the current result page's collection to the metadata of a new post under the `wp_collection` post type. Note that a collection large than 100 assets will only save the 100 assets on the current results page.

## Defining Custom Image Sizes

This plugin ships with some default image sizes however a filter has been included if you wish to change those defined sizes from within another plugin or theme.

Example function defining 2 image sizes:

```php
function filter_defined_image_sizes( $default_image_sizes ) {
  $sizes = [
    'banner-sm' => [
      'label'  => __( 'Small Banner', 'textdomain' ),
      'width'  => 820,
      'height' => 312,
    ],
    'banner-lg' => [
      'label'  => __( 'Large Banner', 'textdomain' ),
      'width'  => 1500,
      'height' => 500,
    ],
  ];

  // Uncomment to append sizes to the plugin's already defined sizes.
  // $sizes = array_merge( $sizes, $default_image_sizes );

  return $sizes;
}
add_filter( 'wm_defined_image_sizes', 'filter_defined_image_sizes', 10, 1 );
```

## Plugin API

This plugin provides some function to allow other plugins to easly interact with Widen data imported into WordPress as well as the Widen API.

### wm_get_collections()

Returns an array of collection objects.

### wm_get_collection( int $collection_id )

Returns the collection object.

### wm_get_asset_fields( int $asset_id )

Returns all the fields from Widen for an asset that exists within the WordPress Media Library.

### wm_get_asset_field( int $asset_id, string $key, bool $single = false )

Returns a single field for a Widen asset that exists within the WordPress Media Library.
