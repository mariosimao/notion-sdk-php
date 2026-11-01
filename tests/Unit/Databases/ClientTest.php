<?php

namespace Notion\Test\Unit\Databases;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\DatabaseType;
use Notion\DataSources\Properties\RichTextProperty;
use Notion\Exceptions\DatabaseException;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type DatabaseJson from \Notion\Databases\Database
 */
class ClientTest extends TestCase
{
    public function test_create_typed_database_sends_database_type_without_title(): void
    {
        $mock = new MockHandler([ new Response(200, [], $this->databaseJson("tasks")) ]);
        $notion = $this->notion($mock);

        $database = Database::create($this->parent(), DatabaseType::Tasks);
        $created = $notion->databases()->create($database);

        $payload = $this->lastPayload($mock);
        $this->assertSame("tasks", $payload["database_type"]);
        $this->assertArrayNotHasKey("title", $payload);
        $this->assertArrayNotHasKey("initial_data_source", $payload);
        $this->assertSame(DatabaseType::Tasks, $created->databaseType);
    }

    public function test_create_typed_database_with_title(): void
    {
        $mock = new MockHandler([ new Response(200, [], $this->databaseJson("projects")) ]);
        $notion = $this->notion($mock);

        $database = Database::create($this->parent(), DatabaseType::Projects)
            ->changeTitle("Roadmap");
        $notion->databases()->create($database);

        $payload = $this->lastPayload($mock);
        $this->assertSame("projects", $payload["database_type"]);
        $this->assertArrayHasKey("title", $payload);
    }

    public function test_create_regular_database_does_not_send_database_type(): void
    {
        $mock = new MockHandler([ new Response(200, [], $this->databaseJson(null)) ]);
        $notion = $this->notion($mock);

        $database = Database::create($this->parent());
        $created = $notion->databases()->create($database, [
            "Description" => RichTextProperty::create("Description"),
        ]);

        $payload = $this->lastPayload($mock);
        $this->assertArrayNotHasKey("database_type", $payload);
        $this->assertArrayHasKey("initial_data_source", $payload);
        $this->assertNull($created->databaseType);
    }

    public function test_create_typed_database_with_initial_properties_fails(): void
    {
        $mock = new MockHandler([]);
        $notion = $this->notion($mock);

        $database = Database::create($this->parent(), DatabaseType::Skills);

        $this->expectException(DatabaseException::class);
        $notion->databases()->create($database, [
            "Description" => RichTextProperty::create("Description"),
        ]);
    }

    public function test_update_does_not_send_database_type(): void
    {
        $mock = new MockHandler([ new Response(200, [], $this->databaseJson("tasks")) ]);
        $notion = $this->notion($mock);

        /** @var DatabaseJson $data */
        $data = json_decode($this->databaseJson("tasks"), true);
        $database = Database::fromArray($data)->changeTitle("New title");

        $updated = $notion->databases()->update($database);

        $payload = $this->lastPayload($mock);
        $this->assertArrayNotHasKey("database_type", $payload);
        $this->assertSame(DatabaseType::Tasks, $updated->databaseType);
    }

    private function parent(): DatabaseParent
    {
        return DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
    }

    private function notion(MockHandler $mock): Notion
    {
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);

        return Notion::createFromConfig($config);
    }

    /** @return array<string, mixed> */
    private function lastPayload(MockHandler $mock): array
    {
        $request = $mock->getLastRequest();
        $this->assertNotNull($request);

        /** @var array<string, mixed> */
        return json_decode((string) $request->getBody(), true);
    }

    private function databaseJson(string|null $databaseType): string
    {
        return json_encode([
            "object" => "database",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "data_sources" => [[
                "id" => "058d158b-09de-4d69-be07-901c20a7ca5c",
                "name" => "Tasks",
            ]],
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "in_trash" => false,
            "title" => [],
            "description" => [],
            "icon" => null,
            "cover" => null,
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
            "is_inline" => false,
            "database_type" => $databaseType,
        ]);
    }
}
