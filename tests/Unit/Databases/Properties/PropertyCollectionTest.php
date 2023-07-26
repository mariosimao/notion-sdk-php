<?php

namespace Notion\Test\Unit\Databases\Properties;

use DateTimeImmutable;
use Exception;
use Notion\Databases\Properties\PropertyCollection;
use Notion\Databases\Properties;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class PropertyCollectionTest extends TestCase
{
    public function test_create(): void
    {
        $collection = PropertyCollection::create(
            Properties\Title::create("Product"),
            Properties\Number::create("Price"),
        );

        $this->assertCount(2, $collection->getAll());
    }

    public function test_add(): void
    {
        $c = PropertyCollection::create(
            Properties\Title::create("Product"),
            Properties\Number::create("Price"),
        )->add(Properties\Number::create("Quantity"));

        $this->assertCount(3, $c->getAll());
    }

    public function test_change(): void
    {
        $c = PropertyCollection::create(
            Properties\Title::create("Movie"),
            Properties\Number::create("Release Date"),
        )->change(Properties\Date::create("Release Date"));

        $this->assertTrue($c->get("Release Date")->metadata()->type === PropertyType::Date);
    }

    public function test_remove(): void
    {
        $c = PropertyCollection::create(
            Properties\Title::create("Product"),
            Properties\Number::create("Price"),
        )->remove("Product");

        $this->assertCount(1, $c->getAll());
    }

    public function test_get(): void
    {
        $product = Properties\Title::create("Product");
        $price = Properties\Number::create("Price");
        $c = PropertyCollection::create($product, $price);

        $this->assertSame($product, $c->get("Product"));
    }

    public function test_get_not_found(): void
    {
        $c = PropertyCollection::create();

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->get("Product");
    }

    public function test_get_by_id(): void
    {
        $prop = Properties\Number::fromArray([
            "id"    => "abc",
            "name"  => "Price",
            "type"  => "number",
            "number" => [
                "format" => "dollar",
            ],
        ]);

        $c = PropertyCollection::create($prop);

        $this->assertSame($prop, $c->getById("abc"));
    }

    public function test_get_by_id_not_found(): void
    {
        $c = PropertyCollection::create();

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->getById("abc");
    }

    public function test_get_checkbox(): void
    {
        $p = Properties\Checkbox::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCheckbox("Name"));
    }

    public function test_get_created_by(): void
    {
        $p = Properties\CreatedBy::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCreatedBy("Name"));
    }

    public function test_get_created_time(): void
    {
        $p = Properties\CreatedTime::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCreatedTime("Name"));
    }

    public function test_get_date(): void
    {
        $p = Properties\Date::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getDate("Name"));
    }

    public function test_get_email(): void
    {
        $p = Properties\Email::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getEmail("Name"));
    }

    public function test_get_files(): void
    {
        $p = Properties\Files::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getFiles("Name"));
    }

    public function test_get_formula(): void
    {
        $p = Properties\Formula::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getFormula("Name"));
    }

    public function test_get_last_edited_by(): void
    {
        $p = Properties\LastEditedBy::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getLastEditedBy("Name"));
    }

    public function test_get_last_edited_time(): void
    {
        $p = Properties\LastEditedTime::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getLastEditedTime("Name"));
    }

    public function test_get_multi_select(): void
    {
        $p = Properties\MultiSelect::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getMultiSelect("Name"));
    }

    public function test_get_number(): void
    {
        $p = Properties\Number::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getNumber("Name"));
    }

    public function test_get_people(): void
    {
        $p = Properties\People::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getPeople("Name"));
    }

    public function test_get_phone_number(): void
    {
        $p = Properties\PhoneNumber::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getPhoneNumber("Name"));
    }

    public function test_get_relation(): void
    {
        $p = Properties\Relation::createUnidirectional("Name", "abc123");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getRelation("Name"));
    }

    public function test_get_rich_text(): void
    {
        $p = Properties\RichTextProperty::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getRichText("Name"));
    }

    public function test_get_select(): void
    {
        $p = Properties\Select::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getSelect("Name"));
    }

    public function test_get_status(): void
    {
        $p = Properties\Status::fromArray([
            "id"    => "abc",
            "name"  => "Name",
            "type"  => "status",
            "status" => [
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "green", "name" => "To-do" ],
                    [ "id" => "222", "option_ids" => ["bbb"], "color" => "yellow", "name" => "In Progress" ],
                    [ "id" => "333", "option_ids" => ["ccc"], "color" => "red", "name" => "Complete" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                    [ "id" => "ccc", "name" => "Option C", "color" => "default" ],
                ],
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getStatus("Name"));
    }

    public function test_get_title(): void
    {
        $p = Properties\Title::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getTitle("Name"));
    }

    public function test_get_url(): void
    {
        $p = Properties\Url::create("Name");

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getUrl("Name"));
    }

    /////// GET BY ID

    public function test_get_checkbox_by_id(): void
    {
        $p = Properties\Checkbox::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "checkbox",
            "checkbox" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCheckboxById("abc"));
    }

    public function test_get_created_by_by_id(): void
    {
        $p = Properties\CreatedBy::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_by",
            "created_by" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCreatedByById("abc"));
    }

    public function test_get_created_time_by_id(): void
    {
        $p = Properties\CreatedTime::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_time",
            "created_time" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getCreatedTimeById("abc"));
    }

    public function test_get_date_by_id(): void
    {
        $p = Properties\Date::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "date",
            "date" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getDateById("abc"));
    }

    public function test_get_email_by_id(): void
    {
        $p = Properties\Email::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "email",
            "email" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getEmailById("abc"));
    }

    public function test_get_files_by_id(): void
    {
        $p = Properties\Files::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "files",
            "files" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getFilesById("abc"));
    }

    public function test_get_formula_by_id(): void
    {
        $p = Properties\Formula::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "formula",
            "formula" => [
                "expression" => "if(prop(\"In stock\"), 0, prop(\"Price\"))",
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getFormulaById("abc"));
    }

    public function test_get_last_edited_by_by_id(): void
    {
        $p = Properties\LastEditedBy::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "last_edited_by",
            "last_edited_by" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getLastEditedByById("abc"));
    }

    public function test_get_last_edited_time_by_id(): void
    {
        $p = Properties\LastEditedTime::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "last_edited_time",
            "last_edited_time" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getLastEditedTimeById("abc"));
    }

    public function test_get_multi_select_by_id(): void
    {
        $p = Properties\MultiSelect::fromArray([
            "id"    => "abc",
            "name"  => "MultiSelect",
            "type"  => "multi_select",
            "multi_select" => [
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                ],
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getMultiSelectById("abc"));
    }

    public function test_get_number_by_id(): void
    {
        $p = Properties\Number::fromArray([
            "id"    => "abc",
            "name"  => "Price",
            "type"  => "number",
            "number" => [
                "format" => "dollar",
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getNumberById("abc"));
    }

    public function test_get_people_by_id(): void
    {
        $p = Properties\People::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "people",
            "people" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getPeopleById("abc"));
    }

    public function test_get_phone_number_by_id(): void
    {
        $p = Properties\PhoneNumber::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "phone_number",
            "phone_number" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getPhoneNumberById("abc"));
    }

    public function test_get_relation_by_id(): void
    {
        $p = Properties\Relation::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "relation",
            "relation" => [
                "database_id" => "84660ad0-9cb9-45d0-aae0-91e2c2526e12",
                "type" => "single_property",
                "single_property" => new \stdClass(),
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getRelationById("abc"));
    }

    public function test_get_rich_text_by_id(): void
    {
        $p = Properties\RichTextProperty::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "rich_text",
            "rich_text" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getRichTextById("abc"));
    }

    public function test_get_select_by_id(): void
    {
        $p = Properties\Select::fromArray([
            "id"    => "abc",
            "name"  => "Select",
            "type"  => "select",
            "select" => [
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                ],
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getSelectById("abc"));
    }

    public function test_get_status_by_id(): void
    {
        $p = Properties\Status::fromArray([
            "id"    => "abc",
            "name"  => "Status",
            "type"  => "status",
            "status" => [
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "green", "name" => "To-do" ],
                    [ "id" => "222", "option_ids" => ["bbb"], "color" => "yellow", "name" => "In Progress" ],
                    [ "id" => "333", "option_ids" => ["ccc"], "color" => "red", "name" => "Complete" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                    [ "id" => "ccc", "name" => "Option C", "color" => "default" ],
                ],
            ],
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getStatusById("abc"));
    }

    public function test_get_title_by_id(): void
    {
        $p = Properties\Title::fromArray([
            "id"    => "title",
            "name"  => "dummy",
            "type"  => "title",
            "title" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getTitleById("title"));
    }

    public function test_get_url_by_id(): void
    {
        $p = Properties\Url::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "url",
            "url" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getUrlById("abc"));
    }

    public function test_get_unique_id(): void
    {
        $p = Properties\UniqueId::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "unique_id",
            "unique_id" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getUniqueId("dummy"));
    }

    public function test_get_unique_id_by_id(): void
    {
        $p = Properties\UniqueId::fromArray([
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "unique_id",
            "unique_id" => new \stdClass(),
        ]);

        $c = PropertyCollection::create($p);

        $this->assertSame($p, $c->getUniqueIdById("abc"));
    }
}
