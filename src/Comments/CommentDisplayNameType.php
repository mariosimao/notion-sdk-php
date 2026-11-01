<?php

namespace Notion\Comments;

enum CommentDisplayNameType: string
{
    case Connection = "connection";
    case User = "user";
    case Custom = "custom";
}
