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
                    ->changeCaption($caption);
```

![](../images/bookmark-caption.png)

## Change URL

```php
$bookmark = Bookmark::create("https://notion.so");
$bookmark = $bookmark->changeUrl("https://google.com");

echo $bookmark->url;
```

Output
```
https://google.com
```

## Change caption

```php
$oldCaption = RichText::createText("An awesome bookmark caption");
$bookmark = Bookmark::create("https://notion.so")
                    ->changeCaption($oldCaption);

$newCaption = RichText::createText("A new caption!");
$bookmark = $bookmark->changeCaption($newCaption);

echo RichText::multipleToString($bookmark->caption);
```

Output
```
A new caption!
```