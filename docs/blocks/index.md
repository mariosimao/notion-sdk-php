# Blocks

## Introduction

Blocks are one of the main objects from the Notion. They are used to compose the
contents of a page. All available blocks are listed [bellow](#available-blocks).

All block objects have the `metadata()` method, witch exposes some metadata.

```php
$p = Paragraph::fromString("Simple paragraph.");

$p->metadta->id;                              // a9f03ee5...
$p->metadta->createdTime->format("Y-m-d");    // 2022-07-01
$p->metadta->lastEditedTime->format("Y-m-d"); // 2022-07-01
$p->metadta->archived;                        // false
$p->metadta->hasChildren;                     // false
```

## Available blocks:

- [Bookmark](./Bookmark)
- [Breadcrumb](./Breadcrumb)
- [BulletedListItem](./BulletedListItem)
- [Callout](./Callout)
- [ChildDatabase](./ChildDatabase)
- [ChildPage](./ChildPage)
- [Client](./Client)
- [Code](./Code)
- [Column](./Column)
- [ColumnList](./ColumnList)
- [Divider](./Divider)
- [Embed](./Embed)
- [EquationBlock](./EquationBlock)
- [FileBlock](./FileBlock)
- [Heading1](./Heading1)
- [Heading2](./Heading2)
- [Heading3](./Heading3)
- [Image](./Image)
- [LinkPreview](./LinkPreview)
- [NumberedListItem](./NumberedListItem)
- [Paragraph](./Paragraph)
- Pdf
- Quote
- TableOfContents
- ToDo
- Toggle
- Video