<?php

namespace Notion\DataSources\Properties;

/**
 * @psalm-type RollupJson = array{
 *      id: string,
 *      name: string,
 *      type: "rollup",
 *      rollup: array{
 *          function: string,
 *          relation_property_name?: string|null,
 *          relation_property_id?: string|null,
 *          rollup_property_name?: string|null,
 *          rollup_property_id?: string|null,
 *      },
 *      description?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Rollup implements PropertyInterface
{
    private function __construct(
        private PropertyMetadata $metadata,
        public RollupFunction $function,
        public string|null $relationPropertyName,
        public string|null $relationPropertyId,
        public string|null $rollupPropertyName,
        public string|null $rollupPropertyId,
    ) {
    }

    public static function create(
        string $propertyName,
        string $relationPropertyName,
        string $rollupPropertyName,
        RollupFunction $function = RollupFunction::ShowOriginal,
    ): self {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Rollup);

        return new self(
            $metadata,
            $function,
            $relationPropertyName,
            null,
            $rollupPropertyName,
            null,
        );
    }

    public static function createById(
        string $propertyName,
        string $relationPropertyId,
        string $rollupPropertyId,
        RollupFunction $function = RollupFunction::ShowOriginal,
    ): self {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Rollup);

        return new self(
            $metadata,
            $function,
            null,
            $relationPropertyId,
            null,
            $rollupPropertyId,
        );
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeFunction(RollupFunction $function): self
    {
        return new self(
            $this->metadata,
            $function,
            $this->relationPropertyName,
            $this->relationPropertyId,
            $this->rollupPropertyName,
            $this->rollupPropertyId,
        );
    }

    public function changeRelationPropertyName(string $relationPropertyName): self
    {
        return new self(
            $this->metadata,
            $this->function,
            $relationPropertyName,
            $this->relationPropertyId,
            $this->rollupPropertyName,
            $this->rollupPropertyId,
        );
    }

    public function changeRelationPropertyId(string $relationPropertyId): self
    {
        return new self(
            $this->metadata,
            $this->function,
            $this->relationPropertyName,
            $relationPropertyId,
            $this->rollupPropertyName,
            $this->rollupPropertyId,
        );
    }

    public function changeRollupPropertyName(string $rollupPropertyName): self
    {
        return new self(
            $this->metadata,
            $this->function,
            $this->relationPropertyName,
            $this->relationPropertyId,
            $rollupPropertyName,
            $this->rollupPropertyId,
        );
    }

    public function changeRollupPropertyId(string $rollupPropertyId): self
    {
        return new self(
            $this->metadata,
            $this->function,
            $this->relationPropertyName,
            $this->relationPropertyId,
            $this->rollupPropertyName,
            $rollupPropertyId,
        );
    }

    public function changeTargetPropertyName(string $targetPropertyName): self
    {
        return $this->changeRollupPropertyName($targetPropertyName);
    }

    public function changeTargetPropertyId(string $targetPropertyId): self
    {
        return $this->changeRollupPropertyId($targetPropertyId);
    }

    public function targetPropertyName(): string|null
    {
        return $this->rollupPropertyName;
    }

    public function targetPropertyId(): string|null
    {
        return $this->rollupPropertyId;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RollupJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $rollup = $array["rollup"];
        $function = RollupFunction::from($rollup["function"]);
        $relationPropertyName = $rollup["relation_property_name"] ?? null;
        $relationPropertyId = $rollup["relation_property_id"] ?? null;
        $rollupPropertyName = $rollup["rollup_property_name"] ?? null;
        $rollupPropertyId = $rollup["rollup_property_id"] ?? null;

        return new self(
            $metadata,
            $function,
            $relationPropertyName,
            $relationPropertyId,
            $rollupPropertyName,
            $rollupPropertyId,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $rollup = [
            "function" => $this->function->value,
        ];

        if ($this->relationPropertyName !== null) {
            $rollup["relation_property_name"] = $this->relationPropertyName;
        }

        if ($this->relationPropertyId !== null) {
            $rollup["relation_property_id"] = $this->relationPropertyId;
        }

        if ($this->rollupPropertyName !== null) {
            $rollup["rollup_property_name"] = $this->rollupPropertyName;
        }

        if ($this->rollupPropertyId !== null) {
            $rollup["rollup_property_id"] = $this->rollupPropertyId;
        }

        $array["rollup"] = $rollup;

        return $array;
    }
}
