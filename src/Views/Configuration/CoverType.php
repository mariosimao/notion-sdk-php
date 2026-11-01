<?php

namespace Notion\Views\Configuration;

enum CoverType: string
{
    case PageCover        = "page_cover";
    case PageContent      = "page_content";
    case PageContentFirst = "page_content_first";
    case Property         = "property";
}
