<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Verification;
use Notion\Pages\Properties\VerificationState;
use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    public function test_create_verified(): void
    {
        $date = Date::createRange(
            new DateTimeImmutable("2026-09-01T09:00:00Z"),
            new DateTimeImmutable("2026-10-01T09:00:00Z"),
        );

        $verification = Verification::createVerified($date);

        $this->assertTrue($verification->isVerified());
        $this->assertSame(VerificationState::Verified, $verification->state);
        $this->assertSame($date, $verification->date);
        $this->assertNull($verification->verifiedBy);
        $this->assertEquals(PropertyType::Verification, $verification->metadata()->type);
    }

    public function test_create_unverified(): void
    {
        $verification = Verification::createUnverified();

        $this->assertFalse($verification->isVerified());
        $this->assertSame(VerificationState::Unverified, $verification->state);
        $this->assertNull($verification->date);
        $this->assertSame(
            ["id" => "", "type" => "verification", "verification" => ["state" => "unverified", "date" => null]],
            $verification->toArray(),
        );
    }

    public function test_verify_and_unverify(): void
    {
        $date = Date::create(new DateTimeImmutable("2026-09-01T09:00:00Z"));

        $verification = Verification::createUnverified()->verify($date);

        $this->assertTrue($verification->isVerified());
        $this->assertSame($date, $verification->date);

        $verification = $verification->unverify();

        $this->assertSame(VerificationState::Unverified, $verification->state);
        $this->assertNull($verification->date);
    }

    public function test_verified_array_conversion(): void
    {
        $array = [
            "id"   => "vrfy",
            "type" => "verification",
            "verification" => [
                "state" => "verified",
                "date"  => [
                    "start" => "2026-09-01T09:00:00.000000Z",
                    "end"   => "2026-10-01T09:00:00.000000Z",
                ],
                "verified_by" => [
                    "object" => "user",
                    "id"     => "c2f20311-9e54-4d11-8c79-7398424ae41e",
                ],
            ],
        ];

        $verification = Verification::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertInstanceOf(Verification::class, $fromFactory);
        $this->assertEquals($array, $verification->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertTrue($verification->isVerified());
        $this->assertEquals(new DateTimeImmutable("2026-09-01T09:00:00Z"), $verification->date?->start);
        $this->assertEquals(new DateTimeImmutable("2026-10-01T09:00:00Z"), $verification->date?->end);
        $this->assertSame("c2f20311-9e54-4d11-8c79-7398424ae41e", $verification->verifiedBy?->id);
    }

    public function test_verify_resets_verifier(): void
    {
        $verification = Verification::fromArray([
            "id"   => "vrfy",
            "type" => "verification",
            "verification" => [
                "state" => "verified",
                "date"  => null,
                "verified_by" => ["object" => "user", "id" => "c2f20311-9e54-4d11-8c79-7398424ae41e"],
            ],
        ])->verify();

        $this->assertNull($verification->verifiedBy);
        $this->assertSame(
            ["id" => "vrfy", "type" => "verification", "verification" => ["state" => "verified", "date" => null]],
            $verification->toArray(),
        );
    }

    public function test_unverified_from_array(): void
    {
        $verification = Verification::fromArray([
            "id"   => "vrfy",
            "type" => "verification",
            "verification" => [
                "state" => "unverified",
                "date"  => null,
                "verified_by" => null,
            ],
        ]);

        $this->assertSame(VerificationState::Unverified, $verification->state);
        $this->assertNull($verification->date);
        $this->assertNull($verification->verifiedBy);
    }

    public function test_expired_from_array(): void
    {
        $verification = Verification::fromArray([
            "id"   => "vrfy",
            "type" => "verification",
            "verification" => [
                "state" => "expired",
                "date"  => [
                    "start" => "2026-08-01T09:00:00.000Z",
                    "end"   => "2026-09-01T09:00:00.000Z",
                    "time_zone" => null,
                ],
                "verified_by" => ["object" => "user", "id" => "c2f20311-9e54-4d11-8c79-7398424ae41e"],
            ],
        ]);

        $this->assertTrue($verification->isExpired());
        $this->assertFalse($verification->isVerified());
    }

    public function test_null_verification_is_unverified(): void
    {
        $verification = Verification::fromArray([
            "id"   => "vrfy",
            "type" => "verification",
            "verification" => null,
        ]);

        $this->assertSame(VerificationState::Unverified, $verification->state);
    }
}
