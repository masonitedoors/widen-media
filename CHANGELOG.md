# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2019-07-17
### Fixed
- Added back missing documentation
- Added back missing changelog
- Removed developer files that were being included within the published composer package

## [2.0.0] - 2019-07-17
### Added
- Search results now show if an asset was already added to the WordPress Media Library
- Already added assets contain a direct link to view the asset within the WordPress Media Library
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
