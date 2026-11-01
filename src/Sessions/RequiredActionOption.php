<?php

namespace Notion\Sessions;

/**
 * @psalm-type RequiredActionOptionJson = array{
 *      id: "approve"|"reject",
 *      label: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class RequiredActionOption
{
    private function __construct(
        public ActionOption $id,
        public string $label,
    ) {
    }

    /**
     * @param RequiredActionOptionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(ActionOption::from($array["id"]), $array["label"]);
    }

    /** @return RequiredActionOptionJson */
    public function toArray(): array
    {
        return [
            "id" => $this->id->value,
            "label" => $this->label,
        ];
    }
}
