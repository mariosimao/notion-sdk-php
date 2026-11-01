<?php

namespace Notion\Views\Configuration;

/**
 * Cover image source of board and gallery cards
 *
 * @psalm-type CoverJson = array{
 *      type: string,
 *      property_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Cover
{
    private function __construct(
        public CoverType $type,
        public string|null $propertyId,
    ) {
    }

    public static function pageCover(): self
    {
        return new self(CoverType::PageCover, null);
    }

    public static function pageContent(): self
    {
        return new self(CoverType::PageContent, null);
    }

    public static function pageContentFirst(): self
    {
        return new self(CoverType::PageContentFirst, null);
    }

    /** @param string $propertyId Files property used as cover */
    public static function property(string $propertyId): self
    {
        return new self(CoverType::Property, $propertyId);
    }

    /**
     * @param CoverJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(CoverType::from($array["type"]), $array["property_id"] ?? null);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->propertyId !== null) {
            $array["property_id"] = $this->propertyId;
        }

        return $array;
    }
}
