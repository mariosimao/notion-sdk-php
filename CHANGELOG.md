# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## Added
- Support to nullable page properties (#149)
  - Properties: `Date`, `Email`, `Number`, `PhoneNumber`, `Select`, `Url`.
  - New method `isEmpty()` on those properties.

## [1.1.0]

## Added
- Add Query::addSort() (#144)
## Changed
- Improve PropertyFactory Exception message (#141)
- Deprecate Query::changeAddedSort() (#144)
## Documentation
- Correct documentation for `->change...()` methods (#145)
## Internal
- Remove toUpdateArray method from blocks (#136)
- Increase unit test coverage (#137)
- Add Intellij Idea default directory .idea to .gitignore (#142)
- Allow usage of secrets while running tests from forks (#143)

## [1.0.0]

### Added
- Add database description (#125)
- Add support to toggleable headings (#126)
- Add caption to Code block (#127)
- Add support to Status property (#132)
- Icon value object instead of `File|Emoji`
### Changed
- Unify constructor method names (#131)
- Require PHP 8.1
- Enums instead of constants for everything. Example: collor, block type, ...
- Readonly public properties and removal of getters
- Many method signatures were changed

## [1.0.0-beta.1]

### Added
- Add database description (#125)
- Add support to toggleable headings (#126)
- Add caption to Code block (#127)
- Add support to Status property (#132)

### Changed
- Unify constructor method names (#131)

### Documentation
- Express psr/http-client dependency on documentation (#128)
- Document blocks (#129)

## [1.0.0-beta.1]

### Added
- Icon value object instead of `File|Emoji`

### Changed
- Require PHP 8.1
- Enums instead of constants for everything. Example: collor, block type, ...
- Readonly public properties and removal of getters
- Many method signatures were changed

## [0.6.2] - 2022-08-19
### Fixed
- Missing `Files` page property (#105)

### Documentation
- How to add and update page properties (#104)
- How to get page content (#106)

## [0.6.1] - 2022-08-04

### Added
- Documentation website

### Fixed
- Method typo ~~`Block::lastEditedType()`~~ `Block::lastEditedTime()`

## [0.6.0] - 2022-07-04

### Added

- Add URL support to RichText objects (#89)

### Fixed

- Wrong object to array conversion

## [0.5.2] - 2022-06-22

### Fixed
- Handle empty value for select property (#86)

## [0.5.1] - 2022-06-02

### Fixed
- Add support to internal cover image (#80)

## [0.5.0] - 2022-05-12

### Added
- Query database (#5 and #75)

## [0.4.0] - 2022-03-24

### Added
- How to documentation for pages
- Find block (#58)
- Update block (#59)
- Append blocks (#60)
- Delete block (#61)

## Changed
- Notion version to `2022-02-22` (#69)

## [0.3.0] - 2021-12-04

### Added
- Find block children
- Find block children recursively
- Link preview block
- Column and column list blocks

### Changed
- Blocks `withChildren()` methods renamed to `changeChildren()`
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

[0.1.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.1.0
[0.2.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.2.0
[0.3.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.3.0
[0.4.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.4.0
[0.5.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.5.0
[0.5.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.5.1
[0.5.2]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.5.2
[0.6.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.6.0
[0.6.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.6.1
[0.6.2]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v0.6.2
[1.0.0-beta.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.0.0-beta.1
[1.0.0-beta.2]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.0.0-beta.2
[1.0.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.0.0
[1.1.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.1.0
[Unreleased]: https://github.com/mariosimao/notion-sdk-php/compare/v1.1.0...HEAD
