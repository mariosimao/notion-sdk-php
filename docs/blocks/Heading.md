# Heading

Small, medium and big section heading.

## Create from string

```php
$blocks = [
    Heading1::fromString("Heading 1"),
    Heading2::fromString("Heading 2"),
    Heading3::fromString("Heading 3"),
];
```

![](../images/heading-from-string.png)

## Create from `RichText`

```php
$block = Heading1::create(
    RichText::fromString("Heading "),
    RichText::fromString("with ")->italic(),
    RichText::fromString("Rich")->underline(),
    RichText::fromString("Text")->bold(),
);
```

![](../images/heading-from-rich-text.png)

## Convert to string

```php
$block = Heading1::create(
    RichText::fromString("Heading "),
    RichText::fromString("with ")->italic(),
    RichText::fromString("Rich")->underline(),
    RichText::fromString("Text")->bold(),
);

echo $block->toString();
```

Output:
```
Heading with RichText
```

## Change text

```php
$block = Heading1::fromString("Old heading");
$block = $block->changeText(
    RichText::fromString("New "),
    RichText::fromString("heading")->bold(),
);

echo $block->toString();
```

Output:

```
New heading
```

## Add text

```php
$block = Heading1::fromString("Heading");

$block = $block->addText(RichText::fromString(" extended"));

echo $block->toString();
```

Output

```
Heading extended
```
