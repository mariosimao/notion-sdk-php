<?php

namespace Notion\Webhooks;

use DateTimeImmutable;
use JsonException;
use Notion\Exceptions\WebhookException;
use Notion\Webhooks\Data\CommentData;
use Notion\Webhooks\Data\ContentUpdatedData;
use Notion\Webhooks\Data\EventData;
use Notion\Webhooks\Data\FileUploadFailedData;
use Notion\Webhooks\Data\PagePropertiesUpdatedData;
use Notion\Webhooks\Data\ParentData;
use Notion\Webhooks\Data\SchemaUpdatedData;
use Notion\Webhooks\Data\TranscriptDeletedData;
use Notion\Webhooks\Data\ViewCreatedData;
use Notion\Webhooks\Data\ViewUpdatedData;

/**
 * @psalm-import-type AuthorJson from Author
 * @psalm-import-type EntityJson from Entity
 *
 * @psalm-type EventJson = array{
 *     id: string,
 *     timestamp: string,
 *     workspace_id: string,
 *     workspace_name: string,
 *     subscription_id: string,
 *     integration_id: string,
 *     type: string,
 *     authors: list<AuthorJson>,
 *     accessible_by?: list<AuthorJson>,
 *     attempt_number: int,
 *     api_version?: string,
 *     entity: EntityJson,
 *     data?: array<string, mixed>,
 * }
 *
 * @psalm-immutable
 */
final readonly class Event
{
    /**
     * @param list<Author> $authors
     * @param list<Author> $accessibleBy Only present for public integrations.
     * @param EventData|null $data Null for events without data, such as `file_upload.created`.
     */
    private function __construct(
        public string $id,
        public DateTimeImmutable $timestamp,
        public string $workspaceId,
        public string $workspaceName,
        public string $subscriptionId,
        public string $integrationId,
        public EventType $type,
        public array $authors,
        public array $accessibleBy,
        public int $attemptNumber,
        public string|null $apiVersion,
        public Entity $entity,
        public EventData|null $data,
    ) {
    }

    /**
     * Parse a raw webhook request body.
     *
     * Verify the body with {@see Signature::verify()} before trusting the event.
     *
     * @throws WebhookException
     */
    public static function fromJson(string $json): self
    {
        try {
            $array = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw WebhookException::invalidPayload($e);
        }

        if (!is_array($array)) {
            throw WebhookException::invalidPayload();
        }

        /** @psalm-var EventJson $array */
        return self::fromArray($array);
    }

    /**
     * @psalm-param EventJson $array
     *
     * @throws WebhookException
     */
    public static function fromArray(array $array): self
    {
        $type = EventType::tryFrom($array["type"])
            ?? throw WebhookException::unknownEventType($array["type"]);

        return new self(
            $array["id"],
            new DateTimeImmutable($array["timestamp"]),
            $array["workspace_id"],
            $array["workspace_name"],
            $array["subscription_id"],
            $array["integration_id"],
            $type,
            array_map(Author::fromArray(...), $array["authors"]),
            array_map(Author::fromArray(...), $array["accessible_by"] ?? []),
            $array["attempt_number"],
            $array["api_version"] ?? null,
            Entity::fromArray($array["entity"]),
            self::dataFromArray($type, $array["data"] ?? []),
        );
    }

    private static function dataFromArray(EventType $type, array $data): EventData|null
    {
        return match ($type) {
            EventType::PageCreated,
            EventType::PageMoved,
            EventType::PageDeleted,
            EventType::PageUndeleted,
            EventType::PageLocked,
            EventType::PageUnlocked,
            EventType::DatabaseCreated,
            EventType::DatabaseMoved,
            EventType::DatabaseDeleted,
            EventType::DatabaseUndeleted,
            EventType::DataSourceCreated,
            EventType::DataSourceMoved,
            EventType::DataSourceDeleted,
            EventType::DataSourceUndeleted,
            EventType::ViewDeleted => ParentData::fromArray($data),

            EventType::PageContentUpdated,
            EventType::DatabaseContentUpdated,
            EventType::DataSourceContentUpdated => ContentUpdatedData::fromArray($data),

            EventType::PagePropertiesUpdated => PagePropertiesUpdatedData::fromArray($data),

            EventType::DatabaseSchemaUpdated,
            EventType::DataSourceSchemaUpdated => SchemaUpdatedData::fromArray($data),

            EventType::PageTranscriptDeleted => TranscriptDeletedData::fromArray($data),

            EventType::CommentCreated,
            EventType::CommentUpdated,
            EventType::CommentDeleted => CommentData::fromArray($data),

            EventType::ViewCreated => ViewCreatedData::fromArray($data),
            EventType::ViewUpdated => ViewUpdatedData::fromArray($data),

            EventType::FileUploadFailed => FileUploadFailedData::fromArray($data),

            EventType::FileUploadCreated,
            EventType::FileUploadCompleted,
            EventType::FileUploadExpired => null,
        };
    }
}
