# PDF

Embed a PDF file.

## Create from a link

```php
$file = File::createExternal(
    "https://shakespeare.folger.edu/downloads/pdf/hamlet_PDF_FolgerShakespeare.pdf"
);
$block = Pdf::create($file);
```

## Change file

```php
$file = File::createExternal("https://example.com/sample1.pdf");
$block = Pdf::create($file);

// Add to a Notion page...

$newFile = File::creatExternal("https://example.com/sample2.pdf");
$block = $block->changeFile($newFile);
```
