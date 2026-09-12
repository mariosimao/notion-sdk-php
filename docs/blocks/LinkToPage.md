# LinkToPage

Link to a Notion page, database, or comment.

## Create a link to a page

```php
$block = LinkToPage::page("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0");
```

## Create a link to a database

```php
$block = LinkToPage::database("d9824bdc-8445-4327-be8b-5b47500af6ce");
```

## Create a link to a comment

```php
$block = LinkToPage::comment("b530263f-6772-469b-9c71-f9256eb91000");
```

## Change target

```php
$block = $block->changePage("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0");
$block = $block->changeDatabase("d9824bdc-8445-4327-be8b-5b47500af6ce");
$block = $block->changeComment("b530263f-6772-469b-9c71-f9256eb91000");
```
