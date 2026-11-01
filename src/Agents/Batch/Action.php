<?php

namespace Notion\Agents\Batch;

enum Action: string
{
    case UpdateStatus = "update_status";
    case UpdateCreditLimit = "update_credit_limit";
    case Delete = "delete";
}
