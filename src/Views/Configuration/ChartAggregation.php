<?php

namespace Notion\Views\Configuration;

/**
 * @psalm-type ChartAggregationJson = array{
 *      aggregator: string,
 *      property_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ChartAggregation
{
    private function __construct(
        public ChartAggregator $aggregator,
        public string|null $propertyId,
    ) {
    }

    /** Count all rows */
    public static function count(): self
    {
        return new self(ChartAggregator::Count, null);
    }

    public static function create(ChartAggregator $aggregator, string $propertyId): self
    {
        return new self($aggregator, $propertyId);
    }

    /**
     * @param ChartAggregationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(ChartAggregator::from($array["aggregator"]), $array["property_id"] ?? null);
    }

    public function toArray(): array
    {
        $array = [ "aggregator" => $this->aggregator->value ];

        if ($this->propertyId !== null) {
            $array["property_id"] = $this->propertyId;
        }

        return $array;
    }
}
