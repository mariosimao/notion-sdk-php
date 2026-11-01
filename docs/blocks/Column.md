# Column

Columns should be used inside a [ColumnList](./ColumnList) block.

The block's children will define the content of the column.

## Create

```php
$column1 = Column::create(
    Heading1::fromString("Column 1"),
    Paragraph::fromString("This is column 1"),
);
$column2 = Column::create(
    Heading1::fromString("Column 2"),
    Paragraph::fromString("This is column 2"),
);

$columnList = ColumnList::create($column1, $column2);
```

![](../images/column-create.png)

## Add child block

```php
Column::create(
        Heading1::fromString("Column 1")
    )->addChild(
        Paragraph::fromString("This is column 1")
    );
```

::: tip
All blocks, but `Column` are allowed as children.
:::

## Change children

```php
Column::create()->changeChildren(
    Paragraph::fromString("This is column 1")
);
```

## Width ratio

By default, all columns of a column list have equal widths. Use
`changeWidthRatio()` to customize the width of a column relative to the others
in the same column list. The ratio must be greater than 0 and less than or
equal to 1.

```php
$columnList = ColumnList::create(
    Column::create(Paragraph::fromString("Narrow"))->changeWidthRatio(0.25),
    Column::create(Paragraph::fromString("Wide"))->changeWidthRatio(0.75),
);

$columnList->columns[0]->widthRatio; // 0.25
```

Use `removeWidthRatio()` to go back to the default width.

::: tip
When provided, width ratios of the columns in a column list must add up to 1.
`ColumnList` throws a `ColumnListException` otherwise.
:::
