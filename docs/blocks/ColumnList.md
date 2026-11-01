# Column list

Column lists are blocks that contains [Columns](./Column) as children.

## Create

```php
$text = Paragraph::fromString("Column");
$col1 = Column::create($text);
$col2 = Column::create($text);
$col3 = Column::create($text);

$block = ColumnList::create($col1, $col2, $col3);
```

## Add column

```php
$col1 = Column::create();
$col2 = Column::create();
$col3 = Column::create();

$block = ColumnList::create($col1, $col2);
$block = $block->addChild($col3);
```

## Change columns

```php
$col1 = Column::create();
$col2 = Column::create();
$col3 = Column::create();

$block = ColumnList::create()
    ->changeColumns($col1, $col2, $col3);
```
## Width ratios

Columns can have custom width ratios (see [Column](./Column#width-ratio)). The
column list validates them:

- width ratios can never add up to more than 1;
- when every column has a width ratio, they must add up to exactly 1 (checked
  by `create()` and `changeChildren()`; `addChild()` only checks the upper bound
  so columns can be added one at a time).

```php
$block = ColumnList::create(
    Column::create($text)->changeWidthRatio(0.5),
    Column::create($text)->changeWidthRatio(0.3),
    Column::create($text)->changeWidthRatio(0.2),
);
```
