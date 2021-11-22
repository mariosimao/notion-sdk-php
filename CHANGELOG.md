# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Find block children
### Fixed

### Changed

## [0.2.0] - 2021-11-20
### Added
- Breadcrumb block
- Support discovery of more PSR clients with `php-http/discovery`

### Changed
- Clients require implementations of `RequestFactoryInterface`
- Rename `Notion\Client::createWithPsrClient()` to `createWithPsrImplementations()`
- Rename `Notion\Client` to `Notion\Notion`
- Rename `Notion\Databases\Database::withTitle()` to `withAdvancedTitle()`
- Use `list<RichText>` instead of `...RichText` on
  - `Bookmark::withCaption()`
  - `BulletedListItem::withText()`
  - `Callout::withText()`
  - `Code::withText()`
  - `Heading1::withText()`
  - `Heading2::withText()`
  - `Heading3::withText()`
  - `NumberedListItem::withText()`
  - `Paragraph::withText()`
  - `Quote::withText()`
  - `ToDo::withText()`
  - `Toggle::withText()`
  - `Database::withAdvancedTitle()`
  - `Title::withRichTexts()`
  - `RichTextProperty::withText()`
- Use `list<BlockInterface>` instead of `...BlockInterface` on
  - `BulletedListItem::withChildren()`
  - `Callout::withChildren()`
  - `NumberedListItem::withChildren()`
  - `Paragraph::withChildren()`
  - `Quote::withChildren()`
  - `ToDo::withChildren()`
  - `Toggle::withChildren()`
  - `Notion\Pages\Client::create()`
- Use `list<SelectOption>` instead of `...SelectOption` on
  - `Select::withOptions()`
  - `MultiSelect::withOptions()`
- Use `list<non-empty-string>` instead of `...string` on
  - `MultiSelect::fromIds()` and `MultiSelect::fromNames()`
  - `Relation::create()` and `Relation::withRelations()`
- Use `list<User>` instead of `...User` on
  - `People::create()` and `People::withPeople()`

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
[0.1.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.1.0
[0.2.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.2.0