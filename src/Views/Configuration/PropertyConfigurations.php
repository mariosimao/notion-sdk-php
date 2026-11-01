<?php

namespace Notion\Views\Configuration;

/**
 * @internal
 *
 * @psalm-immutable
 */
final readonly class PropertyConfigurations
{
    /**
     * @param PropertyConfiguration[] $properties
     *
     * @return list<array>|null
     *
     * @psalm-pure
     */
    public static function toArray(array $properties): array|null
    {
        if ($properties === []) {
            return null;
        }

        return array_values(array_map(fn (PropertyConfiguration $p) => $p->toArray(), $properties));
    }

    /**
     * @param PropertyConfiguration[] $properties
     *
     * @return PropertyConfiguration[]
     *
     * @psalm-pure
     */
    public static function add(array $properties, PropertyConfiguration $property): array
    {
        $result = [];
        $replaced = false;
        foreach ($properties as $existing) {
            if ($existing->propertyId === $property->propertyId) {
                $result[] = $property;
                $replaced = true;
                continue;
            }
            $result[] = $existing;
        }

        if (!$replaced) {
            $result[] = $property;
        }

        return $result;
    }
}
