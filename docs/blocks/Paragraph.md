# Paragraph block

## Working with strings

Creating a paragraph from a simple string:
```php
<?php

$paragraph = Paragraph::fromString("Simple paragraph.");
$paragraph->toString(); // "Simple paragraph."
```

![](../images/paragraph.png)

Or creating an empty paragraph:
```php
<?php

$paragraph = Paragraph::create();
$paragraph->toString(); // empty string
```

## Working with `RichText` objects

```php
<?php

// "Simple text" will be bold and italic
$text = RichText::fromString("Simple text")->bold()->italic();

$paragraph = Paragraph::create()->addText($text);
```

![](../images/paragraph-rich-text.png)

While working with multiple texts:

```php
$text = [
    RichText::fromString("Paragraphs can be "),
    RichText::fromString("bold")->bold(),
    RichText::fromString(", "),
    RichText::fromString("underlined")->underline(),
    RichText::fromString(" and much more!"),
];

$paragraph = Paragraph::create()->changeText(...$text);
```

Note that `changeText()` will replace the text on a new instance of `Paragraph`.

![](../images/paragraph-rich-text-multiple.png)
