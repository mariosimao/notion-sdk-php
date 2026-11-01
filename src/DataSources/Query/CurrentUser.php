<?php

namespace Notion\DataSources\Query;

/**
 * Reference to the user associated with the token, resolved by Notion at query time.
 *
 * For internal connections there is no associated user: `contains` matches no
 * entries and `does_not_contain` matches all entries.
 */
enum CurrentUser: string
{
    case Me = "me";
}
