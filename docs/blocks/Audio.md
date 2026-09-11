# Audio

Embed an audio file.

## Create from a link

```php
$file = File::createExternal("https://example.com/podcast.mp3");
$block = Audio::fromFile($file);

// Or create directly from URL:
$block = Audio::fromUrl("https://example.com/podcast.mp3");
```

## Change file

```php
$file = File::createExternal("https://example.com/podcast1.mp3");
$block = Audio::fromFile($file);

// Add to a Notion page...

$newFile = File::createExternal("https://example.com/podcast2.mp3");
$block = $block->changeFile($newFile);
```

## Change caption

```php
$block = $block->changeCaption(RichText::fromString("Episode 1: Introduction"));
```
