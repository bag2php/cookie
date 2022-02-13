# CHANGELOG

All notable changes of the phpstan.el release series are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [v0.7.0]

### Change

 * `Oven` class implements [`ArrayAccess`] instead of `delete()`, `has()`, `get()` methods
 * Add `#[ReturnTypeWillChange]` attributes to support PHP 8.1

[`ArrayAccess`]: https://www.php.net/ArrayAccess


## [v0.6.1]

### Change

 * Modify PHPDoc with `@template` to make `Oven::setTo ()` generic

## [v0.6.0]

### Added

 * Add new `CookieOvenBuilder` class for create `Oven` instance
