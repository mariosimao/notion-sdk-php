# Image

Upload or embed image with a link.

## Upload

The Notion API [currently does not support uploading new files](https://developers.notion.com/docs/working-with-files-and-media#uploading-files-and-media-via-the-notion-api).

## Embed with link

```php
$file = File::createExternal("https://www.placecage.com/640/360.png");
$block = Image::create($file);
```

## Change file

```php
$file = File::createExternal("https://www.placecage.com/640/360.png");
$block = Image::create($file);

// Add to a Notion page...

$newFile = File::creatExternal("https://www.fillmurray.com/640/360.png");
$block = $block->changeFile($newFile);
```