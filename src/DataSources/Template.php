<?php

namespace Notion\DataSources;

/**
 * Page template available in a data source
 *
 * @psalm-type TemplateJson = array{
 *      id: string,
 *      name: string,
 *      is_default: bool
 * }
 *
 * @psalm-immutable
 */
final readonly class Template
{
    private function __construct(
        public string $id,
        public string $name,
        public bool $isDefault,
    ) {
    }

    /** @param TemplateJson $array */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["name"], $array["is_default"]);
    }

    /** @return TemplateJson */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "is_default" => $this->isDefault,
        ];
    }
}
