<?php

namespace Notion\Views;

/**
 * Placement of a new widget inside a dashboard view
 *
 * @psalm-immutable
 */
final readonly class WidgetPlacement
{
    private function __construct(
        private string $type,
        private int|null $rowIndex,
    ) {
    }

    /** @param int|null $rowIndex Index where the new row is inserted. Appended to the end when null. */
    public static function newRow(int|null $rowIndex = null): self
    {
        return new self("new_row", $rowIndex);
    }

    public static function existingRow(int $rowIndex): self
    {
        return new self("existing_row", $rowIndex);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

        if ($this->rowIndex !== null) {
            $array["row_index"] = $this->rowIndex;
        }

        return $array;
    }
}
