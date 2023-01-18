# Page properties

## Introduction

A page is made up of page properties that contain data about the page. You can
use the Notion SDK to retrieve and update information of a page property.

Accourding to Notion documentation:
> If a page object’s Parent object is a database, then the property values
> conform to the database property schema. If a page object is not part of a
> database, then the only property value available for that page is its title.

All available properties are listed [bellow](#available-properties).

## Metadata

All page propety objects have the `metadata()` method, witch exposes
the ID and type of the property.

```php
$property->metadata()->id;  // a9f03ee5...
$property->metadata()->type // instance of Notion\Pages\Property\PropertyType
```

## Add page property

```php
use Notion\Pages\Properties\Number;

// Create property
$price = Number::create(59.99);

// Find page
$pageId = "249c7266-611a-416a-b2d4-2c7a833b6ac1";
$page = $notion->pages()->find($pageId);

// Add property to page
$page = $page->addProperty("Price", $price);

// Send to Notion
$notion->pages->update($page);
```

## Get page property
```php
// Find page
$pageId = "249c7266-611a-416a-b2d4-2c7a833b6ac1";
$page = $notion->pages()->find($pageId);

// Get property
/** @var \Notion\Pages\Properties\Number $releaseDate */
$price = $page->getProperty("Price");

$price->number;             // 59.99
$price->metadata()->id;     // d7b38593-cb6b-410d-8445-0eac9b774fe0
$price->metadata()->type;   // PropertyType::Number (enum)
```

## Update page property

```php
// Retrieve page
$pageId = "249c7266-611a-416a-b2d4-2c7a833b6ac1";
$page = $notion->pages()->find($pageId);

// Get property
/** @var \Notion\Pages\Properties\Number $releaseDate */
$price = $page->getProperty("Price");

// Update property
$price = $price->changeNumber(49.99);
$page = $page->addProperty("Price", $price);

// Send to Notion
$notion->pages->update($page);
```

## Available properties

- Checkbox
- CreatedBy
- CreatedTime
- Date
- Email
- Files
- Formula
- LastEditedBy
- LastEditedTime
- MultiSelect
- Number
- People
- PhoneNumber
- Relation
- RichText
- Select