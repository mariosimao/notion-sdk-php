<?php

namespace Notion\Sessions\Events;

enum ArtifactType: string
{
    case Page = "page";
    case HtmlArtifact = "html_artifact";
}
