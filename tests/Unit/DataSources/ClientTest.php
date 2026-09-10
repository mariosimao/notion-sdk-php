<?php

namespace Notion\Test\Unit\DataSources;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\DataSources\DataSource;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type DataSourceJson from \Notion\DataSources\DataSource
 */
class ClientTest extends TestCase
{
    public function test_delete_sends_patch_with_in_trash_true(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->dataSourceJson()),
        ]);
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);
        $notion = Notion::createFromConfig($config);

        /** @var DataSourceJson $data */
        $data = json_decode($this->dataSourceJson(), true);
        $dataSource = DataSource::fromArray($data);

        $notion->dataSources()->delete($dataSource);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/data_sources/{$dataSource->id}", (string) $request->getUri());

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame(["in_trash" => true], $payload);
    }

    private function dataSourceJson(): string
    {
        return '{
            "object": "data_source",
            "id": "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time": "2020-12-08T12:00:00.000000Z",
            "last_edited_time": "2020-12-08T12:00:00.000000Z",
            "in_trash": false,
            "title": [],
            "description": [],
            "icon": null,
            "properties": {
                "Title": {
                    "id": "title",
                    "name": "Title",
                    "type": "title",
                    "title": {}
                }
            },
            "parent": {
                "type": "database_id",
                "database_id": "1ce62b6f-b7f3-4201-afd0-08acb02e61c6"
            },
            "url": "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e"
        }';
    }
}
