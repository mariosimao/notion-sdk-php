# SyncedBlock

Synced blocks allow syncing identical block content across multiple locations in Notion. There are two variants of a synced block:
- **Original synced block**: The source block where content is authored. It can have children blocks.
- **Reference synced block**: A duplicate block that references the original synced block by its `block_id`.

## Create an original synced block

```php
use Notion\Blocks\Paragraph;
use Notion\Blocks\SyncedBlock;

// Create an original synced block with children
$original = SyncedBlock::createOriginal(
    Paragraph::fromString("Content shared across pages."),
);

// Or create an empty original synced block and add children later
$original = SyncedBlock::createOriginal()
    ->addChild(Paragraph::fromString("Content shared across pages."));
```

## Create a reference synced block

```php
use Notion\Blocks\SyncedBlock;

// Reference by original block ID string
$reference = SyncedBlock::createReference("7af38973-3787-41b3-bd75-0ed3a1edfac9");

// Or reference from an existing SyncedBlock instance
$reference = SyncedBlock::createReference($originalBlock);
```

## Inspect variant

```php
if ($block->isOriginal()) {
    $children = $block->children;
}

if ($block->isReference()) {
    $targetId = $block->originalBlockId();
}
```

## Manage children

Original synced blocks support adding and changing child blocks:

```php
$block = $block->addChild(Paragraph::fromString("Another paragraph"));
$block = $block->changeChildren(
    Paragraph::fromString("New child 1"),
    Paragraph::fromString("New child 2"),
);
```

> [!NOTE]
> Reference synced blocks do not support adding or changing children directly; attempting to do so will throw a `BlockException`.
