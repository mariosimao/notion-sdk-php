<?php

namespace Notion\Views\Configuration;

/**
 * Sub-item display settings of table views
 *
 * @psalm-type SubtaskConfigurationJson = array{
 *      property_id?: string,
 *      display_mode?: string,
 *      filter_scope?: string,
 *      toggle_column_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class SubtaskConfiguration
{
    private function __construct(
        public string|null $propertyId,
        public SubtaskDisplayMode|null $displayMode,
        public SubtaskFilterScope|null $filterScope,
        public string|null $toggleColumnId,
    ) {
    }

    public static function create(): self
    {
        return new self(null, null, null, null);
    }

    /** Explicitly disable sub-item rendering */
    public static function disabled(): self
    {
        return new self(null, SubtaskDisplayMode::Disabled, null, null);
    }

    /**
     * @param SubtaskConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["property_id"] ?? null,
            isset($array["display_mode"]) ? SubtaskDisplayMode::from($array["display_mode"]) : null,
            isset($array["filter_scope"]) ? SubtaskFilterScope::from($array["filter_scope"]) : null,
            $array["toggle_column_id"] ?? null,
        );
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->propertyId !== null) {
            $array["property_id"] = $this->propertyId;
        }
        if ($this->displayMode !== null) {
            $array["display_mode"] = $this->displayMode->value;
        }
        if ($this->filterScope !== null) {
            $array["filter_scope"] = $this->filterScope->value;
        }
        if ($this->toggleColumnId !== null) {
            $array["toggle_column_id"] = $this->toggleColumnId;
        }

        return $array;
    }

    /** @param string $propertyId Self-referencing relation property ID */
    public function changePropertyId(string $propertyId): self
    {
        return new self($propertyId, $this->displayMode, $this->filterScope, $this->toggleColumnId);
    }

    public function changeDisplayMode(SubtaskDisplayMode $displayMode): self
    {
        return new self($this->propertyId, $displayMode, $this->filterScope, $this->toggleColumnId);
    }

    public function changeFilterScope(SubtaskFilterScope $filterScope): self
    {
        return new self($this->propertyId, $this->displayMode, $filterScope, $this->toggleColumnId);
    }

    public function changeToggleColumnId(string $toggleColumnId): self
    {
        return new self($this->propertyId, $this->displayMode, $this->filterScope, $toggleColumnId);
    }
}
