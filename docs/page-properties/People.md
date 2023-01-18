# People

A list of users.

## Get value

```php
// Find page
$page = $notion->pages()->find($pageId);

// Get value from column "Assignee"
/** @var \Notion\Pages\Properties\People $assignees */
$assignees = $page->getProperty("Assignees");

$users = $assignees->users; // array of Users
foreach ($users as $user) {
    echo $user->name;       // e.g. "Mario Simao"
}
```

## Add people property to a apge

```php
// Get users
$assignee1 = $notion->users()->find("b548b6e7-da76-494b-8ede-1febf4796024");
$assignee2 = $notion->users()->find("dc1a838b-4d6f-4343-b012-be3a4d9fd0c6");

// Create property
$assignees = People::create($assignee1, $assignee2);

// Find page and change property
$page = $notion->pages()->find($pageId);
$page = $page->addProperty("Assignees", $assignees);

// Send to Notion
$notion->pages()->update($page);
```

## Update values
```php
// Get property
$page = $notion->pages()->find($pageId);
/** @var \Notion\Pages\Properties\People $assignees */
$assignees = $page->getProperty("Assignees");

// Fetch users
$assignee1 = $notion->users()->find("789653af-844b-4643-ae14-082d09cfc1f8");
$assignee2 = $notion->users()->find("31449c19-1a9b-456c-a63c-1fdc9b6c7c00");
$assignee3 = $notion->users()->find("d320799c-40d1-46e1-953a-e218f96aeefa");

// Add user to the list
$assignees = $assignees->addPerson($assignee3)

// Change the list
$assignees = $assignees->changePeople($assignee1, $assignee2);

// Remove user from the list
$assignees = $assignees->removePerson($assignee2->id);

// Update property and send to Notion
$page = $page->addProperty("Assignee", $assignees);
$notion->pages()->update($page);
```
