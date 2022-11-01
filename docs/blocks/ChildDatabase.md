# Child database

Child databases can only be added to a page by creating a new database.

However, a child database can be retreived from the page content.

```php
$pageId = "3f4ch4...";
$content = $notion->blocks()->findChildren($pageId);

/** @var ChildDatabase */
$childDatabase = $content[0];
echo $childDatabase->title;
```