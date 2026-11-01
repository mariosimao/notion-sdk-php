<?php

namespace Notion\Test\Unit\Users;

use Notion\Users\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function test_verified_email(): void
    {
        $array = [ "email" => "mario@example.com", "email_verified" => true ];

        $person = Person::fromArray($array);

        $this->assertSame("mario@example.com", $person->email);
        $this->assertTrue($person->emailVerified);
        $this->assertTrue($person->isEmailVerified());
        $this->assertSame($array, $person->toArray());
    }

    public function test_unverified_email(): void
    {
        $array = [ "email" => "mario@example.com", "email_verified" => false ];

        $person = Person::fromArray($array);

        $this->assertFalse($person->emailVerified);
        $this->assertFalse($person->isEmailVerified());
        $this->assertSame($array, $person->toArray());
    }

    public function test_missing_email_verification(): void
    {
        $array = [ "email" => "mario@example.com" ];

        $person = Person::fromArray($array);

        $this->assertNull($person->emailVerified);
        $this->assertFalse($person->isEmailVerified());
        $this->assertSame($array, $person->toArray());
    }
}
