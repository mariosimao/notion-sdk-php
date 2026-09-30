<?php

namespace Notion\Pages\Properties;

enum VerificationState: string
{
    case Verified = "verified";
    case Unverified = "unverified";
    case Expired = "expired";
}
