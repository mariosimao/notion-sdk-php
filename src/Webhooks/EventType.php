<?php

namespace Notion\Webhooks;

enum EventType: string
{
    case PageCreated = "page.created";
    case PageContentUpdated = "page.content_updated";
    case PagePropertiesUpdated = "page.properties_updated";
    case PageMoved = "page.moved";
    case PageDeleted = "page.deleted";
    case PageUndeleted = "page.undeleted";
    case PageLocked = "page.locked";
    case PageUnlocked = "page.unlocked";
    case PageTranscriptDeleted = "page.transcription_block.transcript_deleted";

    case DatabaseCreated = "database.created";
    case DatabaseContentUpdated = "database.content_updated";
    case DatabaseSchemaUpdated = "database.schema_updated";
    case DatabaseMoved = "database.moved";
    case DatabaseDeleted = "database.deleted";
    case DatabaseUndeleted = "database.undeleted";

    case DataSourceCreated = "data_source.created";
    case DataSourceContentUpdated = "data_source.content_updated";
    case DataSourceSchemaUpdated = "data_source.schema_updated";
    case DataSourceMoved = "data_source.moved";
    case DataSourceDeleted = "data_source.deleted";
    case DataSourceUndeleted = "data_source.undeleted";

    case CommentCreated = "comment.created";
    case CommentUpdated = "comment.updated";
    case CommentDeleted = "comment.deleted";

    case FileUploadCreated = "file_upload.created";
    case FileUploadCompleted = "file_upload.completed";
    case FileUploadExpired = "file_upload.expired";
    case FileUploadFailed = "file_upload.upload_failed";

    case ViewCreated = "view.created";
    case ViewUpdated = "view.updated";
    case ViewDeleted = "view.deleted";
}
