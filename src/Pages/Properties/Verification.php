<?php

namespace Notion\Pages\Properties;

use Notion\Common\Date as CommonDate;
use Notion\Users\User;

/**
 * @psalm-import-type DateJson from \Notion\Common\Date
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type VerificationJson = array{
 *      id: string,
 *      type: "verification",
 *      verification: array{
 *          state: "verified"|"unverified"|"expired",
 *          date?: DateJson|null,
 *          verified_by?: UserJson|null,
 *      }|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Verification implements PropertyInterface
{
    private function __construct(
        private PropertyMetadata $metadata,
        public VerificationState $state,
        public CommonDate|null $date,
        public User|null $verifiedBy,
    ) {
    }

    public static function createVerified(CommonDate|null $date = null): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Verification);

        return new self($metadata, VerificationState::Verified, $date, null);
    }

    public static function createUnverified(): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Verification);

        return new self($metadata, VerificationState::Unverified, null, null);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var VerificationJson $array */
        $metadata = PropertyMetadata::fromArray($array);
        $verification = $array["verification"];

        if ($verification === null) {
            return new self($metadata, VerificationState::Unverified, null, null);
        }

        $date = isset($verification["date"]) ? CommonDate::fromArray($verification["date"]) : null;
        $verifiedBy = isset($verification["verified_by"]) ? User::fromArray($verification["verified_by"]) : null;

        return new self(
            $metadata,
            VerificationState::from($verification["state"]),
            $date,
            $verifiedBy,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $verification = [
            "state" => $this->state->value,
            "date"  => $this->date?->toArray(),
        ];

        if ($this->verifiedBy !== null) {
            $verification["verified_by"] = $this->verifiedBy->toArray();
        }

        $array["verification"] = $verification;

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    /** Notion records the acting integration as the verifier. */
    public function verify(CommonDate|null $date = null): self
    {
        return new self($this->metadata, VerificationState::Verified, $date, null);
    }

    public function unverify(): self
    {
        return new self($this->metadata, VerificationState::Unverified, null, null);
    }

    public function isVerified(): bool
    {
        return $this->state === VerificationState::Verified;
    }

    public function isExpired(): bool
    {
        return $this->state === VerificationState::Expired;
    }
}
