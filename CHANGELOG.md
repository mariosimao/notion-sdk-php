# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## Fixed
- Error when updating pages with People properties.

## [v1.14.0] 2024-04-22

## Added
- 'Change' methods to page properties (#342)
- Description to property metadata (#365)

### Docs
Update documentation website URL (#364)

### Build
- Bump vite from 3.2.7 to 3.2.8 in /docs (#335)
- Bump squizlabs/php_codesniffer from 3.8.0 to 3.8.1 (#334)
- Bump phpunit/phpunit from 10.5.5 to 10.5.7 (#332)
- Bump vimeo/psalm from 5.18.0 to 5.19.0 (#333)
- Bump phpunit/phpunit from 10.5.7 to 10.5.10 (#344)
- Bump squizlabs/php_codesniffer from 3.8.1 to 3.9.0 (#348)
- Bump vite from 3.2.8 to 3.2.10 in /docs (#363)
- Bump squizlabs/php_codesniffer from 3.9.0 to 3.9.1 (#361)
- Bump phpunit/phpunit from 10.5.10 to 10.5.16 (#362)
- Bump infection/infection from 0.27.9 to 0.27.11 (#359)

## [v1.13.0] 2024-01-10

### Added
- Support to inline databases (#330)

### Build
- Bump infection/infection from 0.27.8 to 0.27.9 (#327)
- Bump squizlabs/php_codesniffer from 3.7.2 to 3.8.0 (#328)
- Bump phpunit/phpunit from 10.5.3 to 10.5.5 (#329)

## [v1.12.0] 2023-12-31

### Added
- Add support to tables (#224)

### Fixed
- Allow DateFilter to filter created_time and last_edited_time
- Add formula to pages notUpdatableProps (#320)
- Use null fallback for missing array keys for optional user properties (#324)

### Documentation
- Fix page property example (#293)
- Recommend static analysers (#304)

### Build
- Bump infection/infection from 0.27.2 to 0.27.8 (#309)
- Bump phpunit/phpunit from 10.3.4 to 10.4.2 (#305)
- Bump postcss from 8.4.24 to 8.4.31 in /docs (#294)
- Bump phpunit/phpunit from 10.4.2 to 10.5.3 (#322)
- Bump vimeo/psalm from 5.15.0 to 5.18.0 (#321)
- Bump php-http/discovery from 1.19.1 to 1.19.2 (#316)
- Bump guzzlehttp/guzzle from 7.8.0 to 7.8.1 (#315)
- Bump brianium/paratest from 7.2.7 to 7.3.1 (#306)
- Fix tests CI (#325)

## [v1.11.1] 2023-09-25

### Fixed
- Error when Notion sends an empty response (#289)
### Build
- Bump brianium/paratest from 7.2.3 to 7.2.4 (#274)
- Bump vimeo/psalm from 5.14.0 to 5.14.1 (#275)
- Bump brianium/paratest from 7.2.4 to 7.2.5 (#277)
- Bump phpunit/phpunit from 10.3.1 to 10.3.2 (#278)
- Bump vimeo/psalm from 5.14.1 to 5.15.0 (#279)
- Bump guzzlehttp/guzzle from 7.7.0 to 7.8.0 (#280)
- Bump brianium/paratest from 7.2.5 to 7.2.6 (#283)
- Bump phpunit/phpunit from 10.3.2 to 10.3.3 (#284)
- Bump phpunit/phpunit from 10.3.3 to 10.3.4 (#286)
- Bump brianium/paratest from 7.2.6 to 7.2.7 (#288)
- Bump infection/infection from 0.27.0 to 0.27.2 (#287)

## [v1.11.0] 2023-08-02

### Added
- UniqueId page and database property (#268)
### Fixed
- Do not update Unique ID property (#269)
- Do not update Rollup properties (#272)
### Build
- Bump vimeo/psalm from 5.13.0 to 5.13.1 (#263)
- Bump phpunit/phpunit from 10.2.2 to 10.2.3 (#262)
- Bump phpunit/phpunit from 10.2.3 to 10.2.4 (#264)
- Bump php-http/discovery from 1.19.0 to 1.19.1 (#266)
- Bump phpunit/phpunit from 10.2.4 to 10.2.6 (#265)
- Bump brianium/paratest from 7.2.2 to 7.2.3 (#267)
- Bump vimeo/psalm from 5.13.1 to 5.14.0 (#271)

## [v1.10.0] 2023-06-27

### Added
- Page properties with empty values (#260)

### Build
- Bump php-http/discovery from 1.18.1 to 1.19.0 (#254)
- Bump brianium/paratest from 7.1.4 to 7.2.0 (#255)
- Bump vimeo/psalm from 5.12.0 to 5.13.0 (#259)
- Bump brianium/paratest from 7.2.0 to 7.2.2 (#258)

### Documentation
- Fix typo on `metadata` method (#253)

## [v1.9.0] 2023-06-15

### Added
- Caption on image block and file objects (#250)

### Fixed
- Do not update CreatedBy prop on pages (#251)

## [v1.8.1] 2023-06-12

### Fixed
- Rich Text mention creation (#241)
- Possible null pointer on RichText (#246)
- Change rich text URL
- Only send to API file name when set (#234)

### Build
- Bump phpunit/phpunit from 10.1.2 to 10.1.3 (#227)
- Bump guzzlehttp/guzzle from 7.5.1 to 7.6.1 (#226)
- Bump php-http/discovery from 1.18.0 to 1.18.1 (#228)
- Bump infection/infection from 0.26.21 to 0.27.0 (#229)
- Bump guzzlehttp/guzzle from 7.6.1 to 7.7.0 (#230)
- Bump vimeo/psalm from 5.11.0 to 5.12.0 (#233)
- Bump vite from 3.1.3 to 3.2.7 in /docs (#236)
- Bump phpunit/phpunit from 10.1.3 to 10.2.2 (#244)

### Test
- Fix file-related tests (#245)

### Chore
- Add sponsors to README (#237)

## [v1.8.0] 2023-05-09

### Added
- Support block as page and database parent (#214)
- `number_with_commas` format in Number database properties (#216)

### Fixed
- API error while updating page file property (#220)

### Build
- Bump php-http/discovery from 1.15.3 to 1.17.0 (#217)
- Bump infection/infection from 0.26.20 to 0.26.21 (#218)
- Bump brianium/paratest from 7.1.3 to 7.1.4 (#221)
- Bump php-http/discovery from 1.17.0 to 1.18.0 (#222)
- Bump vimeo/psalm from 5.9.0 to 5.11.0 (#223)

## [1.7.0] 2023-04-26

### Added
- Support block colors (#209)
- Render blocks as markdown (#207)

### Fixed
- API exception on query all pages (#211)

### Build
- Bump phpunit from 10.0.19 to 10.1.2 (#206)
- Bump infection from 0.26.19 to 0.26.20 (#204)
- Bump paratest from 7.1.2 to 7.1.3 (#205)
- Bump guzzle from 7.5.0 to 7.5.1 (#203)

## [1.6.2]

### Fixed
- Date filter JSON serialziation (#198)

## [1.6.1]

### Fixed
- Do not update created time page property. (#187)

## [1.6.0]

### Added
- This week on date filter for database queries. (#184)
- Relation filter for database queries. (#185)

## [1.5.0]

### Added
- Support unknown blocks and page/database properties. Pages and databases with unsuported resources will be loaded without errors. (#173)
- Page and database properties collection with typed getters. (#179)
- Search pages and databases.

## [1.4.1]

### Fixed
- Fix `Page::getProperty()` typo (#170)

### Chore
- Update dependencies (#171)

## [1.4.0]

### Added
- Support to comments (#163)

### Fixed
- Update options without color (#166)
- Prevent `LastEditedBy` errors on page update (#167)

### Documentation
- Document `People` page property (#162)

## [1.3.0]

### Added
- Configuration support for custom options (#155)
- Retry after `conflict_error` (#155)
- `changeColor` methods on `StatusOption` and `Status` property (#159)

### Fixed
- Status page property update (#159)

### Documentation
- Reflect breaking changes from v1 on the documentation.

## [1.2.0]

### Added
- Support to nullable page properties (#149)
  - Properties: `Date`, `Email`, `Number`, `PhoneNumber`, `Select`, `Url`.
  - New method `isEmpty()` on those properties.
- Support `Relation` database property (#150)

## [1.1.0]

### Added
- Add Query::addSort() (#144)
### Changed
- Improve PropertyFactory Exception message (#141)
- Deprecate Query::changeAddedSort() (#144)
### Documentation
- Correct documentation for `->change...()` methods (#145)
### Internal
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

### Changed
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
[1.2.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.2.0
[1.3.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.3.0
[1.4.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.4.0
[1.4.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.4.1
[1.5.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.5.0
[1.6.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.6.0
[1.6.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.6.1
[1.6.2]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.6.2
[1.7.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.7.0
[1.8.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.8.0
[1.8.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.8.1
[1.9.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.9.0
[1.10.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.10.0
[1.11.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.11.0
[1.11.1]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.11.1
[1.12.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.12.0
[1.13.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.13.0
[1.14.0]: https://github.com/mariosimao/notion-sdk-php/releases/tag/v1.14.0
[Unreleased]: https://github.com/mariosimao/notion-sdk-php/compare/v1.14.0...HEAD
