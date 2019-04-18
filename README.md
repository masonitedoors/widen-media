# Widen Media

> Search & add Widen digital assets to your WordPress media library.

This WordPress plugin lets users search for images in [Widen](https://www.widen.com/) and add them to their site's media library. This plugin does not download media from Widen but rather creates a new WordPress media attachment that refers to the Widen asset URL.

## Installation

You can either [download the zip](https://github.com/masonitedoors/widen-media/archive/master.zip) and install via the WordPress plugin installer or via composer.

### Composer

```shell
composer require masonitedoors/widen-media
```

## Configuration

This plugin uses [V2 of the Widen API](https://widenv2.docs.apiary.io/). Define your Widen API access token in `wp-config.php`.

```php
define( 'WIDEN_MEDIA_ACCESS_TOKEN', 'my-widen-api-token' );
```
