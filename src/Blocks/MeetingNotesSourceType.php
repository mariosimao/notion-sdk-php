<?php

namespace Notion\Blocks;

enum MeetingNotesSourceType: string
{
    case FileUpload = "file_upload";
    case Block = "block";
}
