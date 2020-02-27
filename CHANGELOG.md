# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.5.2] - 2020-02-27

### Fixed

- Fix PHP Warning from incorrect argument when registering custom post type.

## [2.5.1] - 2019-08-29

### Added

- New 64x64px 'pager' size when importing images or collections

## [2.5.0] - 2019-08-05

### Added

- Add new API function wm_get_collections

## [2.4.2] - 2019-08-05

### Fixed

- Fix PHP error when using the wm_get_collection API function

## [2.4.1] - 2019-07-31

### Fixed

- Resolved PHP warning that appears inside of multisite network pages

## [2.4.0] - 2019-07-30

### Added

- Add support to sync an existing collection with Widen

## [2.3.0] - 2019-07-30

### Added

- Prevent duplicate collections from being added
- Indicate if a collection already exists

## [2.2.0] - 2019-07-30

### Added

- Saved collections now thumnail url & include meta fields from Widen
- Added a plugin API that themes can tap into
- Updated plugin docs

### Fixed

- Assets containing spaces in their url now show up as already added when added to the WordPress Media Library

## [2.1.0] - 2019-07-22

### Added

- Users can now search for collections and save to their site

## [2.0.2] - 2019-07-17

### Fixed

- Remove debugging helpers

## [2.0.1] - 2019-07-17

### Fixed

- Added back missing documentation
- Added back missing changelog
- Remove irrelevant files from the published composer package

## [2.0.0] - 2019-07-17

### Added

- Search results now show if an asset was already added to the WordPress Media Library
- Link to view assets already added to the WordPress Media Library
- Search results now are paginated

### Fixed

- Assets can no longer be added twice to the WordPress Media Library.
- Fixed WordPress core handling of URLs when using functions such as `wp_get_attachment_image_src()`
- Remove any "URL building" and only use URL from a Widen response

## [0.0.2] - 2019-04-23

### Added

- Image results now lazyload
- Total search results count is now displayed

### Fixed

- Fixed search would occasionally fail when assets were missing properties

## [0.0.1] - 2019-04-18

### Added

- Initial release of Widen Media plugin
