# Widen Media

Search and add Widen digital assets to your WordPress media library.

## Widen Meta

Some additional Widen metadata is captured and saved to the database on the "Add to Media Library" action. This plugin does not currently support syncing of meta data between Widen & WordPress.

### ID

```php
get_post_meta( $attachment_id, '_widen_media_id', true );
```
