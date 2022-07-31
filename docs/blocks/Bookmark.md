# Bookmark block

## Create

Creating a bookmark from a URL string:
```php
<?php

$bookmark = Bookmark::create("https://notion.so");
```

![](../images/bookmark.png)

## Add caption

```php
$caption = RichText::createText("An awesome bookmark caption");

$bookmark = Bookmark::create("https://notion.so")
                    ->withCaption($caption);
```

![](../images/bookmark-caption.png)

## Change URL

```php
$bookmark = Bookmark::create("https://notion.so");

$bookmark = $bookmark->withUrl("https://google.com");
```