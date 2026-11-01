<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-type FormConfigurationJson = array{
 *      type: "form",
 *      is_form_closed?: bool|null,
 *      anonymous_submissions?: bool|null,
 *      submission_permissions?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class FormConfiguration implements ViewConfiguration
{
    private function __construct(
        public bool|null $isFormClosed,
        public bool|null $anonymousSubmissions,
        public SubmissionPermissions|null $submissionPermissions,
    ) {
    }

    public static function create(): self
    {
        return new self(null, null, null);
    }

    /**
     * @param FormConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["is_form_closed"] ?? null,
            $array["anonymous_submissions"] ?? null,
            isset($array["submission_permissions"])
                ? SubmissionPermissions::from($array["submission_permissions"])
                : null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Form;
    }

    public function toArray(): array
    {
        return [
            "type"                   => $this->type()->value,
            "is_form_closed"         => $this->isFormClosed,
            "anonymous_submissions"  => $this->anonymousSubmissions,
            "submission_permissions" => $this->submissionPermissions?->value,
        ];
    }

    public function close(): self
    {
        return new self(true, $this->anonymousSubmissions, $this->submissionPermissions);
    }

    public function open(): self
    {
        return new self(false, $this->anonymousSubmissions, $this->submissionPermissions);
    }

    public function changeAnonymousSubmissions(bool $anonymousSubmissions): self
    {
        return new self($this->isFormClosed, $anonymousSubmissions, $this->submissionPermissions);
    }

    /** @param SubmissionPermissions $permissions Permission granted to the submitter on the created page */
    public function changeSubmissionPermissions(SubmissionPermissions $permissions): self
    {
        return new self($this->isFormClosed, $this->anonymousSubmissions, $permissions);
    }
}
