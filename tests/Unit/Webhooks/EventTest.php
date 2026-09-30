<?php

namespace Notion\Test\Unit\Webhooks;

use Notion\Exceptions\WebhookException;
use Notion\Webhooks\AuthorType;
use Notion\Webhooks\Data\CommentData;
use Notion\Webhooks\Data\ContentUpdatedData;
use Notion\Webhooks\Data\FileUploadFailedData;
use Notion\Webhooks\Data\PagePropertiesUpdatedData;
use Notion\Webhooks\Data\ParentData;
use Notion\Webhooks\Data\PropertyChangeAction;
use Notion\Webhooks\Data\SchemaUpdatedData;
use Notion\Webhooks\Data\TranscriptDeletedData;
use Notion\Webhooks\Data\ViewCreatedData;
use Notion\Webhooks\Data\ViewUpdatedData;
use Notion\Webhooks\EntityType;
use Notion\Webhooks\Event;
use Notion\Webhooks\EventParentType;
use Notion\Webhooks\EventType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function test_from_json_page_created(): void
    {
        $json = json_encode(self::payload("page.created", ["id" => "page-id", "type" => "page"], [
            "parent" => ["id" => "parent-id", "type" => "page"],
        ]), JSON_THROW_ON_ERROR);

        $event = Event::fromJson($json);

        $this->assertSame("367cba44-b6f3-4c92-81e7-6a2e9659efd4", $event->id);
        $this->assertSame("2024-12-05T23:55:34.285000+00:00", $event->timestamp->format("Y-m-d\TH:i:s.uP"));
        $this->assertSame("13950b26-c203-4f3b-b97d-93ec06319565", $event->workspaceId);
        $this->assertSame("Quantify Labs", $event->workspaceName);
        $this->assertSame("29d75c0d-5546-4414-8459-7b7a92f1fc4b", $event->subscriptionId);
        $this->assertSame("0ef2e755-4912-8096-91c1-00376a88a5ca", $event->integrationId);
        $this->assertSame(EventType::PageCreated, $event->type);
        $this->assertCount(1, $event->authors);
        $this->assertSame("c7c11cca-1d73-471d-9b6e-bdef51470190", $event->authors[0]->id);
        $this->assertSame(AuthorType::Person, $event->authors[0]->type);
        $this->assertCount(2, $event->accessibleBy);
        $this->assertSame(AuthorType::Bot, $event->accessibleBy[1]->type);
        $this->assertSame(1, $event->attemptNumber);
        $this->assertSame("2025-09-03", $event->apiVersion);
        $this->assertSame("page-id", $event->entity->id);
        $this->assertSame(EntityType::Page, $event->entity->type);

        $this->assertInstanceOf(ParentData::class, $event->data);
        $this->assertSame("parent-id", $event->data->parent->id);
        $this->assertSame(EventParentType::Page, $event->data->parent->type);
        $this->assertNull($event->data->parent->dataSourceId);
    }

    public function test_optional_fields_are_missing(): void
    {
        $array = self::payload("page.locked", ["id" => "page-id", "type" => "page"], [
            "parent" => ["id" => "space-id", "type" => "space"],
        ]);
        unset($array["accessible_by"], $array["api_version"]);

        $event = Event::fromArray($array);

        $this->assertSame([], $event->accessibleBy);
        $this->assertNull($event->apiVersion);
    }

    /** @return array<string, array{string, string}> */
    public static function parentOnlyEventProvider(): array
    {
        return [
            "page.created"          => ["page.created", "page"],
            "page.moved"            => ["page.moved", "page"],
            "page.deleted"          => ["page.deleted", "page"],
            "page.undeleted"        => ["page.undeleted", "page"],
            "page.locked"           => ["page.locked", "page"],
            "page.unlocked"         => ["page.unlocked", "page"],
            "database.created"      => ["database.created", "database"],
            "database.moved"        => ["database.moved", "database"],
            "database.deleted"      => ["database.deleted", "database"],
            "database.undeleted"    => ["database.undeleted", "database"],
            "data_source.created"   => ["data_source.created", "data_source"],
            "data_source.moved"     => ["data_source.moved", "data_source"],
            "data_source.deleted"   => ["data_source.deleted", "data_source"],
            "data_source.undeleted" => ["data_source.undeleted", "data_source"],
            "view.deleted"          => ["view.deleted", "view"],
        ];
    }

    #[DataProvider("parentOnlyEventProvider")]
    public function test_parent_only_events(string $type, string $entityType): void
    {
        $event = Event::fromArray(self::payload($type, ["id" => "entity-id", "type" => $entityType], [
            "parent" => ["id" => "database-id", "type" => "database", "data_source_id" => "data-source-id"],
        ]));

        $this->assertSame($type, $event->type->value);
        $this->assertSame($entityType, $event->entity->type->value);
        $this->assertInstanceOf(ParentData::class, $event->data);
        $this->assertSame(EventParentType::Database, $event->data->parent->type);
        $this->assertSame("data-source-id", $event->data->parent->dataSourceId);
    }

    /** @return array<string, array{string, string}> */
    public static function contentUpdatedEventProvider(): array
    {
        return [
            "page.content_updated"        => ["page.content_updated", "page"],
            "database.content_updated"    => ["database.content_updated", "database"],
            "data_source.content_updated" => ["data_source.content_updated", "data_source"],
        ];
    }

    #[DataProvider("contentUpdatedEventProvider")]
    public function test_content_updated_events(string $type, string $entityType): void
    {
        $event = Event::fromArray(self::payload($type, ["id" => "entity-id", "type" => $entityType], [
            "updated_blocks" => [
                ["id" => "block-1", "type" => "block"],
                ["id" => "page-1", "type" => "page"],
            ],
            "parent" => ["id" => "parent-id", "type" => "page"],
        ]));

        $this->assertInstanceOf(ContentUpdatedData::class, $event->data);
        $this->assertSame("parent-id", $event->data->parent->id);
        $this->assertCount(2, $event->data->updatedBlocks);
        $this->assertSame("block-1", $event->data->updatedBlocks[0]->id);
        $this->assertSame(EntityType::Block, $event->data->updatedBlocks[0]->type);
        $this->assertSame(EntityType::Page, $event->data->updatedBlocks[1]->type);
    }

    public function test_page_properties_updated(): void
    {
        $event = Event::fromArray(self::payload("page.properties_updated", ["id" => "page-id", "type" => "page"], [
            "parent" => ["id" => "space-id", "type" => "space"],
            "updated_properties" => ["XGe%40", "bDf%5B", "DbAu"],
        ]));

        $this->assertSame(EventType::PagePropertiesUpdated, $event->type);
        $this->assertInstanceOf(PagePropertiesUpdatedData::class, $event->data);
        $this->assertSame(EventParentType::Space, $event->data->parent->type);
        $this->assertSame(["XGe%40", "bDf%5B", "DbAu"], $event->data->updatedProperties);
    }

    /** @return array<string, array{string, string}> */
    public static function schemaUpdatedEventProvider(): array
    {
        return [
            "database.schema_updated"    => ["database.schema_updated", "database"],
            "data_source.schema_updated" => ["data_source.schema_updated", "data_source"],
        ];
    }

    #[DataProvider("schemaUpdatedEventProvider")]
    public function test_schema_updated_events(string $type, string $entityType): void
    {
        $event = Event::fromArray(self::payload($type, ["id" => "entity-id", "type" => $entityType], [
            "parent" => ["id" => "parent-id", "type" => "page"],
            "updated_properties" => [
                ["id" => "kqLW", "name" => "Created at", "action" => "created"],
                ["id" => "wX%7Bd", "name" => "Blurb", "action" => "updated"],
                ["id" => "LIM%5D", "name" => null, "action" => "deleted"],
            ],
        ]));

        $this->assertInstanceOf(SchemaUpdatedData::class, $event->data);
        $this->assertCount(3, $event->data->updatedProperties);
        $this->assertSame("kqLW", $event->data->updatedProperties[0]->id);
        $this->assertSame("Created at", $event->data->updatedProperties[0]->name);
        $this->assertSame(PropertyChangeAction::Created, $event->data->updatedProperties[0]->action);
        $this->assertSame(PropertyChangeAction::Updated, $event->data->updatedProperties[1]->action);
        $this->assertNull($event->data->updatedProperties[2]->name);
        $this->assertSame(PropertyChangeAction::Deleted, $event->data->updatedProperties[2]->action);
    }

    public function test_schema_updated_without_updated_properties(): void
    {
        $event = Event::fromArray(self::payload("data_source.schema_updated", [
            "id" => "data-source-id",
            "type" => "data_source",
        ], [
            "parent" => ["id" => "parent-id", "type" => "page"],
        ]));

        $this->assertInstanceOf(SchemaUpdatedData::class, $event->data);
        $this->assertSame([], $event->data->updatedProperties);
    }

    /** @return array<string, array{string}> */
    public static function commentEventProvider(): array
    {
        return [
            "comment.created" => ["comment.created"],
            "comment.updated" => ["comment.updated"],
            "comment.deleted" => ["comment.deleted"],
        ];
    }

    #[DataProvider("commentEventProvider")]
    public function test_comment_events(string $type): void
    {
        $event = Event::fromArray(self::payload($type, ["id" => "comment-id", "type" => "comment"], [
            "page_id" => "page-id",
            "discussion_id" => "discussion-id",
            "parent" => ["id" => "block-id", "type" => "block"],
        ]));

        $this->assertSame(EntityType::Comment, $event->entity->type);
        $this->assertInstanceOf(CommentData::class, $event->data);
        $this->assertSame("page-id", $event->data->pageId);
        $this->assertSame("discussion-id", $event->data->discussionId);
        $this->assertSame("block-id", $event->data->parent->id);
        $this->assertSame(EventParentType::Block, $event->data->parent->type);
    }

    public function test_view_created(): void
    {
        $event = Event::fromArray(self::payload("view.created", ["id" => "view-id", "type" => "view"], [
            "parent" => ["id" => "database-id", "type" => "database"],
            "view_type" => "board",
        ]));

        $this->assertSame(EntityType::View, $event->entity->type);
        $this->assertInstanceOf(ViewCreatedData::class, $event->data);
        $this->assertSame("database-id", $event->data->parent->id);
        $this->assertSame("board", $event->data->viewType);
    }

    public function test_view_updated(): void
    {
        $event = Event::fromArray(self::payload("view.updated", ["id" => "view-id", "type" => "view"], [
            "parent" => ["id" => "database-id", "type" => "database"],
            "updated_fields" => ["name", "filter"],
        ]));

        $this->assertInstanceOf(ViewUpdatedData::class, $event->data);
        $this->assertSame(["name", "filter"], $event->data->updatedFields);
    }

    public function test_page_transcript_deleted(): void
    {
        $event = Event::fromArray(self::payload(
            "page.transcription_block.transcript_deleted",
            ["id" => "page-id", "type" => "page"],
            [
                "target" => ["id" => "block-id", "type" => "block"],
                "transcript_id" => "transcript-id",
            ],
        ));

        $this->assertSame(EventType::PageTranscriptDeleted, $event->type);
        $this->assertInstanceOf(TranscriptDeletedData::class, $event->data);
        $this->assertSame("block-id", $event->data->target->id);
        $this->assertSame(EntityType::Block, $event->data->target->type);
        $this->assertSame("transcript-id", $event->data->transcriptId);
    }

    /** @return array<string, array{string}> */
    public static function fileUploadEventWithoutDataProvider(): array
    {
        return [
            "file_upload.created"   => ["file_upload.created"],
            "file_upload.completed" => ["file_upload.completed"],
            "file_upload.expired"   => ["file_upload.expired"],
        ];
    }

    #[DataProvider("fileUploadEventWithoutDataProvider")]
    public function test_file_upload_events_without_data(string $type): void
    {
        $array = self::payload($type, ["id" => "file-upload-id", "type" => "file_upload"], []);
        unset($array["data"]);

        $event = Event::fromArray($array);

        $this->assertSame($type, $event->type->value);
        $this->assertSame(EntityType::FileUpload, $event->entity->type);
        $this->assertNull($event->data);
    }

    public function test_file_upload_failed(): void
    {
        $event = Event::fromArray(self::payload(
            "file_upload.upload_failed",
            ["id" => "file-upload-id", "type" => "file_upload"],
            [
                "file_import_result" => [
                    "type" => "error",
                    "imported_time" => "2025-01-01T00:00:00.000Z",
                    "error" => [
                        "type" => "download_error",
                        "code" => "file_not_found",
                        "message" => "File could not be downloaded.",
                        "parameter" => null,
                        "status_code" => 404,
                    ],
                ],
            ],
        ));

        $this->assertSame(EventType::FileUploadFailed, $event->type);
        $this->assertInstanceOf(FileUploadFailedData::class, $event->data);
        $result = $event->data->fileImportResult;
        $this->assertFalse($result->isSuccess());
        $this->assertSame("2025-01-01", $result->importedTime->format("Y-m-d"));
        $error = $result->error;
        $this->assertNotNull($error);
        $this->assertSame("download_error", $error->type);
        $this->assertSame("file_not_found", $error->code);
        $this->assertSame("File could not be downloaded.", $error->message);
        $this->assertNull($error->parameter);
        $this->assertSame(404, $error->statusCode);
    }

    public function test_file_import_success(): void
    {
        $event = Event::fromArray(self::payload(
            "file_upload.upload_failed",
            ["id" => "file-upload-id", "type" => "file_upload"],
            [
                "file_import_result" => [
                    "type" => "success",
                    "imported_time" => "2025-01-01T00:00:00.000Z",
                    "success" => [],
                ],
            ],
        ));

        $this->assertInstanceOf(FileUploadFailedData::class, $event->data);
        $this->assertTrue($event->data->fileImportResult->isSuccess());
        $this->assertNull($event->data->fileImportResult->error);
    }

    public function test_unknown_event_type(): void
    {
        $this->expectException(WebhookException::class);
        $this->expectExceptionMessage("Unknown webhook event type \"page.renamed\".");

        Event::fromArray(self::payload("page.renamed", ["id" => "page-id", "type" => "page"], []));
    }

    public function test_invalid_json(): void
    {
        $this->expectException(WebhookException::class);

        Event::fromJson("{not json");
    }

    public function test_json_that_is_not_an_object(): void
    {
        $this->expectException(WebhookException::class);

        Event::fromJson("\"page.created\"");
    }

    /**
     * @param array{id: string, type: string} $entity
     * @param array<string, mixed> $data
     *
     * @return array{
     *     id: string,
     *     timestamp: string,
     *     workspace_id: string,
     *     workspace_name: string,
     *     subscription_id: string,
     *     integration_id: string,
     *     type: string,
     *     authors: list<array{id: string, type: string}>,
     *     accessible_by?: list<array{id: string, type: string}>,
     *     attempt_number: int,
     *     api_version?: string,
     *     entity: array{id: string, type: string},
     *     data?: array<string, mixed>,
     * }
     */
    private static function payload(string $type, array $entity, array $data): array
    {
        return [
            "id" => "367cba44-b6f3-4c92-81e7-6a2e9659efd4",
            "timestamp" => "2024-12-05T23:55:34.285Z",
            "workspace_id" => "13950b26-c203-4f3b-b97d-93ec06319565",
            "workspace_name" => "Quantify Labs",
            "subscription_id" => "29d75c0d-5546-4414-8459-7b7a92f1fc4b",
            "integration_id" => "0ef2e755-4912-8096-91c1-00376a88a5ca",
            "type" => $type,
            "authors" => [
                ["id" => "c7c11cca-1d73-471d-9b6e-bdef51470190", "type" => "person"],
            ],
            "accessible_by" => [
                ["id" => "556a1abf-4f08-40c6-878a-75890d2a88ba", "type" => "person"],
                ["id" => "1edc05f6-2702-81b5-8408-00279347f034", "type" => "bot"],
            ],
            "attempt_number" => 1,
            "api_version" => "2025-09-03",
            "entity" => $entity,
            "data" => $data,
        ];
    }
}
