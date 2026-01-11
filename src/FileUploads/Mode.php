<?php

namespace Notion\FileUploads;

enum Mode: string
{
    case SinglePart = "single_part";
    case MultiPart = "multi_part";
    case ExternalUrl = "external_url";
}
