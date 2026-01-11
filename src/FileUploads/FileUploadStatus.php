<?php

namespace Notion\FileUploads;

enum FileUploadStatus: string
{
    case Pending = 'pending';
    case Uploaded = 'uploaded';
    case Expired = 'expired';
    case Failed = 'failed';
}
