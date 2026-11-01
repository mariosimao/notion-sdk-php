<?php

namespace Notion\Blocks;

/**
 * Language hint used to transcribe meeting notes.
 */
enum MeetingNotesLanguage: string
{
    case Auto = "auto";
    case English = "en";
    case ChineseSimplified = "zh-CN";
    case ChineseTraditional = "zh-TW";
    case Spanish = "es";
    case French = "fr";
    case German = "de";
    case Japanese = "ja";
    case Korean = "ko";
    case Portuguese = "pt";
    case Russian = "ru";
    case Thai = "th";
    case Vietnamese = "vi";
    case Indonesian = "id";
    case Danish = "da";
    case Finnish = "fi";
    case Norwegian = "no";
    case Dutch = "nl";
    case Italian = "it";
    case Swedish = "sv";
    case Arabic = "ar";
    case Hebrew = "he";
    case Polish = "pl";
}
