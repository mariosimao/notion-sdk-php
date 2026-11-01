<?php

namespace Notion\Agents;

/**
 * Value returned by the API in place of fields the integration is not allowed to read.
 */
enum Hidden: string
{
    case Hidden = "hidden";
}
