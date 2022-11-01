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