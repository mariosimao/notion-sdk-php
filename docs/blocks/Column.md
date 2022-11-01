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
