# Bulleted list item

## Create empty item

```php
$item = BulletedListItem::create();
```

## Create from string

Bulleted list items can be created from simple strings.

```php
$item = BulletedListItem::fromString("Item content");

echo $item->toString(); // "Item content"
```

![](../images/bulleted-list-item.jpg)

## Create from `RichText`

```php
$text = RichText::createText("Item text")->italic();

$item = BulletedListItem::create()->withText($text);
```

![](../images/bulleted-list-item-rich-text.jpg)

## Add text

```php
$item = BulletedListItem::fromString("Item text");
$item = $item->withText(
    RichText::createText(" can be extended!")
);

echo $item->toString(); // "Item text can be extended!"
```

## Add child

```php
$item = BulletedListItem::fromString("Item text");

$item = $item->appendChild(
    Paragraph::fromString("A simple child paragraph.")
);
```

![](../images/bulleted-list-item-append-child.jpg)

## Change children

```php
$item = BulletedListItem::fromString("Item text");

$item = $item->changeChildren([
    Paragraph::fromString("Child paragraph 1"),
    Paragraph::fromString("Child paragraph 2"),
]);
```

![](../images/bulleted-list-item-change-children.jpg)
