# Template

Template blocks represent template buttons in the Notion UI. They contain rich text for the button title and nested child blocks that are duplicated when the button is clicked in the UI.

## Create

```php
use Notion\Blocks\Paragraph;
use Notion\Blocks\Template;
use Notion\Common\RichText;

// Create an empty template block
$template = Template::create();

// Create a template block with title text
$template = Template::create(RichText::fromString("Add a new item"));
```

## Add child block

```php
$template = Template::create(RichText::fromString("Add a new item"))
    ->addChild(Paragraph::fromString("Item description"));
```

## Change children

```php
$template = $template->changeChildren(
    Paragraph::fromString("First child block"),
    Paragraph::fromString("Second child block"),
);
```

## Change text

```php
$template = $template->changeText(RichText::fromString("Updated button label"));
```
