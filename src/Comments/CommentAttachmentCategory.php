<?php

namespace Notion\Comments;

enum CommentAttachmentCategory: string
{
    case Audio = "audio";
    case Image = "image";
    case Pdf = "pdf";
    case Productivity = "productivity";
    case Video = "video";
}
