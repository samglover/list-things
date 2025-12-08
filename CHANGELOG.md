# Changelog for the List Things Plugin

All notable changes to this project will be documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com) and uses [semantic versioning](https://semver.org/).

## 0.4.0 - 2025-12-07

### Added
- Pagination!

### Changed
- The files in /common now comply with the WordPress PHP coding standards
- Added `.thing-content__container` to make grid/card layout more simple
- Simplified layout styles and made them easier to override


## 0.3.1 - 2025-02-03

### Fixed
* Add max-width: 100% to search container rows so they wrap at narrow widths.
* Sort buttons now wrap, but button text no longer wraps.


## 0.3 - 2025-02-02

### Added
* Search and sort functionality.

*Note: If using action hooks from a them or another plugin, the callback function must be available in the admin, or it will not work with the AJAX function that refreshes the list of things.*


## 0.2 - 2024-12-??

### Changed
* Enqueue stylesheets and scripts in the shortcode rather than trying to use `has_shortcode()`, which fails when adding a shortcode from a template file with `do_shortcode()`.

### Added
* The `list-child-pages` shortcode can now exclude child pages by using the `post__not_in` attribute.
* Three new action hooks for customizing the list of things:
  `list_things_before_title`
  `list_things_after_title`
  `list_things_after_excerpt`
* `post__in` shortcode attribute can be used to add a comma-separated list of post IDs.
* `spacing` shortcode attribute now adds a class intended for spacing elements. Supports `gap-xxs` through `gap-xxl` by default. Uses margins instead of `gap` for .layout-list.
* `classes` shortcode attribute can be used to add custom classes to the .list-of-things__container element.


## 0.1 - 2024-09-16

### Changed
* Grid breakpoints updated.
* Thing thumbnails in grid layouts are now constrained to a 16:9 aspect ratio.