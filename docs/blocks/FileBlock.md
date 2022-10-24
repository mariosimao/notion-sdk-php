# File

Upload or embed with a link.

## Create

```php
$file = File::createExternal(
    "https://shakespeare.folger.edu/downloads/pdf/hamlet_PDF_FolgerShakespeare.pdf"
);
$block = FileBlock::create($file);
```

![](../images/file-block.png)
