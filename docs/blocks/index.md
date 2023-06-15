# Blocks

## Introduction

Blocks are one of the main objects from the Notion. They are used to compose the
contents of a page. All available blocks are listed [bellow](#available-blocks).

## Metadata

All block objects have the `metadata()` method, witch exposes some metadata.

```php
$p = Paragraph::fromString("Simple paragraph.");

$p->metadata()->id;                              // a9f03ee5...
$p->metadata()->createdTime->format("Y-m-d");    // 2022-07-01
$p->metadata()->lastEditedTime->format("Y-m-d"); // 2022-07-01
$p->metadata()->archived;                        // false
$p->metadata()->hasChildren;                     // false
```

## Children

Some blocks additionally support adding or changing children. Children can be
any other type of block.

```php
/* Add child block */
$c = Callout::fromString("💡", "A brilliant idea");
$p = Paragraph::fromString("Simple paragraph.")
        ->addChild($c);

/* Change children blocks */
$p = $p->changeChildren(
    Paragraph::fromString("Nested paragraph 1"),
    Paragraph::fromString("Nested paragraph 2"),
);
count($p->children); // 2

/* Remove children blocks */
$p = $p->changeChildren();
```

## Available blocks

| Block                                  | Support children |
|----------------------------------------|:----------------:|
| [Bookmark](./Bookmark)                 | ❌              |
| [Breadcrumb](./Breadcrumb)             | ❌              |
| [BulletedListItem](./BulletedListItem) | ✔               |
| [Callout](./Callout)                   | ✔               |
| [ChildDatabase](./ChildDatabase)       | ❌              |
| [ChildPage](./ChildPage)               | ❌              |
| [Code](./Code)                         | ❌              |
| [Column](./Column)                     | ✔               |
| [ColumnList](./ColumnList)             | ✔               |
| [Divider](./Divider)                   | ❌              |
| [Embed](./Embed)                       | ❌              |
| [EquationBlock](./EquationBlock)       | ❌              |
| [FileBlock](./FileBlock)               | ❌              |
| [Heading1](./Heading)                  | ✔               |
| [Heading2](./Heading)                  | ✔               |
| [Heading3](./Heading)                  | ✔               |
| [Image](./Image)                       | ❌              |
| [LinkPreview](./LinkPreview)           | ❌              |
| [NumberedListItem](./NumberedListItem) | ✔               |
| [Paragraph](./Paragraph)               | ✔               |
| [PDF](./Pdf.md)                        | ❌              |
| [Quote](./Quote.md)                    | ✔               |
| TableOfContents                        | ❌              |
| ToDo                                   | ✔               |
| Toggle                                 | ✔               |
| Video                                  | ❌              |