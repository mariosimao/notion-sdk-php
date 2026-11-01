<?php

namespace Notion\Views\Configuration;

enum SubmissionPermissions: string
{
    case None         = "none";
    case CommentOnly  = "comment_only";
    case Reader       = "reader";
    case ReadAndWrite = "read_and_write";
    case Editor       = "editor";
}
