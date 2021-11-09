# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Breadcrumb block
- Support discovery of more PSR clients with `php-http/discovery`

### Changed
- Clients require implementations of `RequestFactoryInterface`
- Renamed `Notion\Client::createWithPsrClient()` to `createWithPsrImplementations()`

## [0.1.0] - 2021-11-03
### Added
- Support to pages, databases and users API.
- Blocks
  - bookmark
  - bulleted list item
  - callout
  - child database
  - child page
  - code
  - divider
  - embed
  - equation
  - file
  - heading 1
  - heading 2
  - heading 3
  - image
  - numbered list item
  - paragraph
  - PDF
  - quote
  - table of contents
  - to do
  - toggle
  - video

- Database and Page properties:
  - checkbox
  - created by
  - created time
  - date
  - email
  - files
  - formula
  - last edited by
  - last edited time
  - multi select
  - number
  - people
  - phone number
  - rich text
  - select
  - title
  - URL

[Unreleased]: https://github.com/mariosimao/notion-sdk-php/compare/v0.1.0...HEAD
[0.0.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.0.1