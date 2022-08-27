<?php

namespace Notion\Users;

enum UserType: string
{
    case Person = "person";
    case Bot = "bot";
}
