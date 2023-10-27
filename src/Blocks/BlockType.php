<?php

namespace Notion\Blocks;

enum BlockType: string
{
    case Paragraph = "paragraph";
    case Heading1 = "heading_1";
    case Heading2 = "heading_2";
    case Heading3 = "heading_3";
    case Callout = "callout";
    case Quote = "quote";
    case BulletedListItem = "bulleted_list_item";
    case NumberedListItem = "numbered_list_item";
    case ToDo = "to_do";
    case Toggle = "toggle";
    case Code = "code";
    case ChildPage = "child_page";
    case ChildDatabase = "child_database";
    case Embed = "embed";
    case Image = "image";
    case Video = "video";
    case File = "file";
    case Pdf = "pdf";
    case Bookmark = "bookmark";
    case Equation = "equation";
    case Divider = "divider";
    case Table = "table";
    case TableRow = "table_row";
    case TableOfContents = "table_of_contents";
    case Breadcrumb = "breadcrumb";
    case Column = "column";
    case ColumnList = "column_list";
    case LinkPreview = "link_preview";
    case Unknown = "unknown";
}
