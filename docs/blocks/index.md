# Blocks

## Introduction

Blocks are one of the main objects from the Notion. They are used to compose the
contents of a page. All available blocks are listed [bellow](#available-blocks).

All block objects have the `block()` method, witch exposes some metadata.

```php
$p = Paragraph::fromString("Simple paragraph.");

$p->block()->id();                              // a9f03ee534d745d7a0c1f834572cc49f
$p->block()->createdTime()->format("Y-m-d");    // 2022-07-01
$p->block()->lastEditedTime()->format("Y-m-d"); // 2022-07-16
$p->block()->archived();                        // false
$p->block()->hasChildren();                     // false
```

## Available blocks:

- [Bookmark](./Bookmark)
- Breadcrumb
- [BulletedListItem](./BulletedListItem)
- Callout
- ChildDatabase
- ChildPage
- Client
- Code
- Column
- ColumnList
- Divider
- Embed
- EquationBlock
- FileBlock
- Heading1
- Heading2
- Heading3
- Image
- LinkPreview
- NumberedListItem
- [Paragraph](./Paragraph)
- Pdf
- Quote
- TableOfContents
- ToDo
- Toggle
- Video