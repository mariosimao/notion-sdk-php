<?php

namespace Notion\Common;

enum RichTextType: string
{
    case Equation = "equation";
    case Mention = "mention";
    case Text = "text";
}
