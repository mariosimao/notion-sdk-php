# Child page

Child pages can only be added to a page by creating a new page.

However, a child page can be retreived from the page content.

```php
$pageId = "3f4ch4...";
$content = $notion->blocks()->findChildren($pageId);

/** @var ChildPage */
$childPage = $content[0];
echo $childPage->title;
```