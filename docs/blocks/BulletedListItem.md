# Bulleted list item

## Create empty item

```php
$item = BulletedListItem::create();
```

## Create from string

Bulleted list items can be created from simple strings.

```php
$item = BulletedListItem::fromString("Item content");
```

![](../images/bulleted-list-item.jpg)

## Create from `RichText`

```php
$text = RichText::createText("Item text")->italic();

$item = BulletedListItem::create()->changeText($text);
```

![](../images/bulleted-list-item-rich-text.jpg)

## Add text

```php
$item = BulletedListItem::fromString("Item text");
$item = $item->addText(
    RichText::createText(" can be extended!")
);

echo $item->toString();
```

Output:
```
Item text can be extended!
```
## Add child

```php
$item = BulletedListItem::fromString("Item text");

$item = $item->addChild(
    Paragraph::fromString("A simple child paragraph.")
);
```

![](../images/bulleted-list-item-append-child.jpg)

## Change children

```php
$item = BulletedListItem::fromString("Item text")
    ->addChild(Paragraph::fromString("Old child"));

$item = $item->changeChildren(
    Paragraph::fromString("Child paragraph 1"),
    Paragraph::fromString("Child paragraph 2"),
);
```

![](../images/bulleted-list-item-change-children.jpg)

## Convert to string

Get item content as string

```php
$item = BulletedListItem::fromString("Item text");

echo $item->toString();
```

Output:

```
Item text
```