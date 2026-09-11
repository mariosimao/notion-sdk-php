<?php

namespace Notion\Test\Unit\Authentication;

use Notion\Authentication\ExternalAccount;
use PHPUnit\Framework\TestCase;

class ExternalAccountTest extends TestCase
{
    public function test_create(): void
    {
        $externalAccount = ExternalAccount::create("account_123", "Acme Corporation");

        $this->assertSame("account_123", $externalAccount->key);
        $this->assertSame("Acme Corporation", $externalAccount->name);
    }

    public function test_to_array(): void
    {
        $externalAccount = ExternalAccount::create("account_789", "Initech");

        $expected = [
            "key" => "account_789",
            "name" => "Initech",
        ];

        $this->assertSame($expected, $externalAccount->toArray());
    }
}
