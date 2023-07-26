<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Exception;
use Notion\Pages\Properties\PropertyCollection;
use Notion\Pages\Properties;
use PHPUnit\Framework\TestCase;
use TypeError;

class PropertyCollectionTest extends TestCase
{
    public function test_create(): void
    {
        $properties = [
            "Product" => Properties\RichTextProperty::fromString("Apple"),
            "Price" => Properties\Number::create(4.99),
        ];

        $collection = PropertyCollection::create($properties);

        $this->assertCount(2, $collection->getAll());
    }

    public function test_add(): void
    {
        $properties = [
            "Product" => Properties\RichTextProperty::fromString("Apple"),
            "Price" => Properties\Number::create(4.99),
        ];

        $c = PropertyCollection::create($properties)
            ->add("Quantity", Properties\Number::create(3));

        $this->assertSame(3, $c->getNumber("Quantity")->number);
    }

    public function test_change(): void
    {
        $c = PropertyCollection::create([
            "Product" => Properties\RichTextProperty::fromString("Apple"),
            "Price" => Properties\Number::create(4.99),
        ])->change("Price", Properties\Number::create(3.99));

        $this->assertSame(3.99, $c->getNumber("Price")->number);
    }

    public function test_get(): void
    {
        $c = PropertyCollection::create([
            "Product" => Properties\RichTextProperty::fromString("Apple"),
            "Price" => Properties\Number::create(4.99),
        ]);

        /** @var Properties\RichTextProperty */
        $prop = $c->get("Product");

        $this->assertSame("Apple", $prop->toString());
    }

    public function test_get_not_found(): void
    {
        $c = PropertyCollection::create([]);

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->get("Product");
    }

    public function test_get_by_id(): void
    {
        $prop = Properties\Number::fromArray([
            "id" => "abc123",
            "type" => "number",
            "number" => 42,
        ]);

        $c = PropertyCollection::create([ "Answer" => $prop ]);

        $this->assertSame($prop, $c->getById("abc123"));
    }

    public function test_get_by_id_not_found(): void
    {
        $c = PropertyCollection::create([]);

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->getById("abc");
    }

    public function test_title(): void
    {
        $c = PropertyCollection::create([
            "Title" => Properties\Title::fromString("Avatar")
        ]);

        $this->assertSame("Avatar", $c->title()?->toString());
    }

    public function test_null_title(): void
    {
        $c = PropertyCollection::create([]);

        $this->assertNull($c->title());
    }

    public function test_get_typed_wrong_type(): void
    {
        $prop = Properties\Checkbox::createChecked();

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->expectException(TypeError::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->getNumber("Name");
    }

    public function test_get_typed_by_id_wrong_type(): void
    {
        $prop = Properties\Checkbox::fromArray([
            "id" => "abc",
            "type" => "checkbox",
            "checkbox" => true,
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->expectException(TypeError::class);
        /** @psalm-suppress UnusedMethodCall */
        $c->getNumberById("abc");
    }

    public function test_get_checkbox_by_id(): void
    {
        $prop = Properties\Checkbox::fromArray([
            "id" => "abc",
            "type" => "checkbox",
            "checkbox" => true,
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCheckboxById("abc"));
    }

    public function test_get_checkbox(): void
    {
        $prop = Properties\Checkbox::createChecked();

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCheckbox("Name"));
    }

    public function test_get_created_by_by_id(): void
    {
        $prop = Properties\CreatedBy::fromArray([
            "id" => "abc",
            "type" => "created_by",
            "created_by" => [
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "avatar_url" => null,
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCreatedByById("abc"));
    }

    public function test_get_created_by(): void
    {
        $prop = Properties\CreatedBy::fromArray([
            "id" => "abc",
            "type" => "created_by",
            "created_by" => [
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "avatar_url" => null,
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCreatedBy("Name"));
    }

    public function test_get_created_time_by_id(): void
    {
        $prop = Properties\CreatedTime::fromArray([
            "id" => "abc",
            "type" => "created_time",
            "created_time" => "2021-01-01T00:00:00.000000Z",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCreatedTimeById("abc"));
    }

    public function test_get_created_time(): void
    {
        $prop = Properties\CreatedTime::create(new DateTimeImmutable("2023-01-01"));

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getCreatedTime("Name"));
    }

    public function test_get_date_by_id(): void
    {
        $prop = Properties\Date::fromArray([
            "id"   => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "date",
            "date" => [
                "start" => "2021-01-01T00:00:00.000000Z",
                "end"   => "2021-12-31T00:00:00.000000Z",
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getDateById("a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5"));
    }

    public function test_get_date(): void
    {
        $prop = Properties\Date::create(new DateTimeImmutable("2023-01-01"));

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getDate("Name"));
    }

    public function test_get_email_by_id(): void
    {
        $prop = Properties\Email::fromArray([
            "id" => "abc",
            "type" => "email",
            "email" => "mario@domain.com",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getEmailById("abc"));
    }

    public function test_get_email(): void
    {
        $prop = Properties\Email::create("test@example.com");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getEmail("Name"));
    }

    public function test_get_files_by_id(): void
    {
        $prop = Properties\Files::fromArray([
            "id" => "abc",
            "type" => "files",
            "files" => [
                [
                    "type" => "external",
                    "external" => [
                        "url"  => "https://example.com/image.png",
                    ],
                ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getFilesById("abc"));
    }

    public function test_get_files(): void
    {
        $prop = Properties\Files::create();

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getFiles("Name"));
    }

    public function test_get_formula_by_id(): void
    {
        $prop = Properties\Formula::fromArray([
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "number",
                "number" => 123,
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getFormulaById("a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5"));
    }

    public function test_get_formula(): void
    {
        $prop = Properties\Formula::fromArray([
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "string",
                "string" => "Formula result",
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getFormula("Name"));
    }

    public function test_get_last_edited_by_by_id(): void
    {
        $prop = Properties\LastEditedBy::fromArray([
            "id" => "abc",
            "type" => "last_edited_by",
            "last_edited_by" => [
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "avatar_url" => null,
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getLastEditedByById("abc"));
    }

    public function test_get_last_edited_by(): void
    {
        $prop = Properties\LastEditedBy::fromArray([
            "id" => "abc",
            "type" => "last_edited_by",
            "last_edited_by" => [
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "avatar_url" => null,
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getLastEditedBy("Name"));
    }

    public function test_get_last_edited_time_by_id(): void
    {
        $prop = Properties\LastEditedTime::fromArray([
            "id" => "abc",
            "type" => "last_edited_time",
            "last_edited_time" => "2021-01-01T00:00:00.000000Z",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getLastEditedTimeById("abc"));
    }

    public function test_get_last_edited_time(): void
    {
        $prop = Properties\LastEditedTime::fromArray([
            "id" => "abc",
            "type" => "last_edited_time",
            "last_edited_time" => "2021-01-01T00:00:00.000000Z",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getLastEditedTime("Name"));
    }

    public function test_get_multi_select_by_id(): void
    {
        $prop = Properties\MultiSelect::fromArray([
            "id" => "931db25b-f8af-4fc0-b7bf-eb9c29de6b87",
            "type" => "multi_select",
            "multi_select" => [
                [ "name" => "Option A", "color" => "red" ],
                [ "name" => "Option C", "color" => "blue" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getMultiSelectById("931db25b-f8af-4fc0-b7bf-eb9c29de6b87"));
    }

    public function test_get_multi_select(): void
    {
        $prop = Properties\MultiSelect::fromNames("Orange", "Banana");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getMultiSelect("Name"));
    }

    public function test_get_number_by_id(): void
    {
        $prop = Properties\Number::fromArray([
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "number",
            "number" => 123,
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getNumberById("a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5"));
    }

    public function test_get_number(): void
    {
        $prop = Properties\Number::create(123);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getNumber("Name"));
    }

    public function test_get_people_by_id(): void
    {
        $prop = Properties\People::fromArray([
            "id" => "abc",
            "type" => "people",
            "people" => [
                [
                    "id" => "f98bfb6a-08b3-4e65-861b-6f68fb0c7a48",
                    "name" => "Mario",
                    "avatar_url" => null,
                    "type" => "person",
                    "person" => [ "email" => "mario@website.domain" ],
                ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getPeopleById("abc"));
    }

    public function test_get_people(): void
    {
        $prop = Properties\People::create();

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getPeople("Name"));
    }

    public function test_get_phone_number_by_id(): void
    {
        $prop = Properties\PhoneNumber::fromArray([
            "id" => "abc",
            "type" => "phone_number",
            "phone_number" => "415-000-1111",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getPhoneNumberById("abc"));
    }

    public function test_get_phone_number(): void
    {
        $prop = Properties\PhoneNumber::create("+551140028922");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getPhoneNumber("Name"));
    }

    public function test_get_relation_by_id(): void
    {
        $prop = Properties\Relation::fromArray([
            "id" => "abc",
            "type" => "relation",
            "relation" => [
                [ "id" => "264f3f43-3d87-4bb2-bb66-11812ab74eae" ],
                [ "id" => "f3902b7f-e9e2-4406-8c3f-5d07dbc87d66" ],
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getRelationById("abc"));
    }

    public function test_get_relation(): void
    {
        $prop = Properties\Relation::create();

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getRelation("Name"));
    }

    public function test_get_rich_text_by_id(): void
    {
        $prop = Properties\RichTextProperty::fromArray([
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "rich_text",
            "rich_text" => [[
                "plain_text" => "Dummy text",
                "href" => null,
                "annotations" => [
                    "bold"          => false,
                    "italic"        => false,
                    "strikethrough" => false,
                    "underline"     => false,
                    "code"          => false,
                    "color"         => "default",
                ],
                "type" => "text",
                "text" => [
                    "content" => "Dummy text",
                ],
            ]],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getRichTextById("a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5"));
    }

    public function test_get_rich_text(): void
    {
        $prop = Properties\RichTextProperty::fromString("Hi!");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getRichText("Name"));
    }

    public function test_get_select_by_id(): void
    {
        $prop = Properties\Select::fromArray([
            "id"     => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"   => "select",
            "select" => [
                "name"  => "Option A",
                "id"    => "ad762674-9280-444b-96a7-3a0fb0aefff9",
                "color" => "default",
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getSelectById("a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5"));
    }

    public function test_get_select(): void
    {
        $prop = Properties\Select::fromName("Blue");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getSelect("Name"));
    }

    public function test_get_status_by_id(): void
    {
        $prop = Properties\Status::fromArray([
            "id"     => "ec27421d-cd03-4843-b8e9-ea08702d54ac",
            "type"   => "status",
            "status" => [
                "id"    => "032b00eb-228c-4ee3-ba1d-fb6e8a42cc95",
                "name"  => "Done",
                "color" => "default"
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getStatusById("ec27421d-cd03-4843-b8e9-ea08702d54ac"));
    }

    public function test_get_status(): void
    {
        $prop = Properties\Status::fromName("Done");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getStatus("Name"));
    }

    public function test_get_url_by_id(): void
    {
        $prop = Properties\Url::fromArray([
            "id" => "abc",
            "type" => "url",
            "url" => "https://notion.so",
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getUrlById("abc"));
    }

    public function test_get_url(): void
    {
        $prop = Properties\Url::create("https://example.com");

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getUrl("Name"));
    }

    public function test_get_unique_id_by_id(): void
    {
        $prop = Properties\UniqueId::fromArray([
            "id" => "abc",
            "type" => "unique_id",
            "unique_id" => [
                "number" => 123,
                "prefix" => "ISSUE",
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getUniqueIdById("abc"));
    }

    public function test_get_unique_id(): void
    {
        $prop = Properties\UniqueId::fromArray([
            "id" => "abc",
            "type" => "unique_id",
            "unique_id" => [
                "number" => 123,
                "prefix" => "ISSUE",
            ],
        ]);

        $c = PropertyCollection::create([ "Name" => $prop ]);

        $this->assertSame($prop, $c->getUniqueId("Name"));
    }
}
