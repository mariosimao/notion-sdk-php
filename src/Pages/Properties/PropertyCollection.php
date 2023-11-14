<?php

namespace Notion\Pages\Properties;

/** @psalm-immutable */
final class PropertyCollection
{
    /**
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        private readonly array $properties,
    ) {
    }

    /**
     * @param array<string, PropertyInterface> $properties
     *
     * @psalm-mutation-free
     */
    public static function create(array $properties): self
    {
        return new self($properties);
    }

    public function add(string $propertyName, PropertyInterface $property): self
    {
        $properties = $this->properties;
        $properties[$propertyName] = $property;

        return new self($properties);
    }

    public function change(string $propertyName, PropertyInterface $property): self
    {
        return $this->add($propertyName, $property);
    }

    public function get(string $propertyName): PropertyInterface
    {
        if (array_key_exists($propertyName, $this->properties)) {
            return $this->properties[$propertyName];
        }

        throw new \Exception("Property '{$propertyName}' not found");
    }

    public function getById(string $propertyId): PropertyInterface
    {
        foreach ($this->properties as $property) {
            if ($property->metadata()->id === $propertyId) {
                return $property;
            }
        }

        throw new \Exception("Property '{$propertyId}' not found.");
    }

    /** @return array<string, PropertyInterface> */
    public function getAll(): array
    {
        return $this->properties;
    }

    public function title(): Title|null
    {
        foreach ($this->properties as $property) {
            if ($property::class === Title::class) {
                return $property;
            }
        }

        return null;
    }

    public function titleKey(): string
    {
        foreach ($this->properties as $key => $property) {
            if ($property::class === Title::class) {
                return $key;
            }
        }

        return "title";
    }

    public function getCheckbox(string $propertyName): Checkbox
    {
        return $this->getTyped($propertyName, Checkbox::class);
    }

    public function getCheckboxById(string $propertyId): Checkbox
    {
        return $this->getTypedById($propertyId, Checkbox::class);
    }

    public function getCreatedBy(string $propertyName): CreatedBy
    {
        return $this->getTyped($propertyName, CreatedBy::class);
    }

    public function getCreatedByById(string $propertyId): CreatedBy
    {
        return $this->getTypedById($propertyId, CreatedBy::class);
    }

    public function getCreatedTime(string $propertyName): CreatedTime
    {
        return $this->getTyped($propertyName, CreatedTime::class);
    }

    public function getCreatedTimeById(string $propertyId): CreatedTime
    {
        return $this->getTypedById($propertyId, CreatedTime::class);
    }

    public function getDate(string $propertyName): Date
    {
        return $this->getTyped($propertyName, Date::class);
    }

    public function getDateById(string $propertyId): Date
    {
        return $this->getTypedById($propertyId, Date::class);
    }

    public function getEmail(string $propertyName): Email
    {
        return $this->getTyped($propertyName, Email::class);
    }

    public function getEmailById(string $propertyId): Email
    {
        return $this->getTypedById($propertyId, Email::class);
    }

    public function getFiles(string $propertyName): Files
    {
        return $this->getTyped($propertyName, Files::class);
    }

    public function getFilesById(string $propertyId): Files
    {
        return $this->getTypedById($propertyId, Files::class);
    }

    public function getFormula(string $propertyName): Formula
    {
        return $this->getTyped($propertyName, Formula::class);
    }

    public function getFormulaById(string $propertyId): Formula
    {
        return $this->getTypedById($propertyId, Formula::class);
    }

    public function getLastEditedBy(string $propertyName): LastEditedBy
    {
        return $this->getTyped($propertyName, LastEditedBy::class);
    }

    public function getLastEditedByById(string $propertyId): LastEditedBy
    {
        return $this->getTypedById($propertyId, LastEditedBy::class);
    }

    public function getLastEditedTime(string $propertyName): LastEditedTime
    {
        return $this->getTyped($propertyName, LastEditedTime::class);
    }

    public function getLastEditedTimeById(string $propertyId): LastEditedTime
    {
        return $this->getTypedById($propertyId, LastEditedTime::class);
    }

    public function getMultiSelect(string $propertyName): MultiSelect
    {
        return $this->getTyped($propertyName, MultiSelect::class);
    }

    public function getMultiSelectById(string $propertyId): MultiSelect
    {
        return $this->getTypedById($propertyId, MultiSelect::class);
    }

    public function getNumber(string $propertyName): Number
    {
        return $this->getTyped($propertyName, Number::class);
    }

    public function getNumberById(string $propertyId): Number
    {
        return $this->getTypedById($propertyId, Number::class);
    }

    public function getPeople(string $propertyName): People
    {
        return $this->getTyped($propertyName, People::class);
    }

    public function getPeopleById(string $propertyId): People
    {
        return $this->getTypedById($propertyId, People::class);
    }

    public function getPhoneNumber(string $propertyName): PhoneNumber
    {
        return $this->getTyped($propertyName, PhoneNumber::class);
    }

    public function getPhoneNumberById(string $propertyId): PhoneNumber
    {
        return $this->getTypedById($propertyId, PhoneNumber::class);
    }

    public function getRelation(string $propertyName): Relation
    {
        return $this->getTyped($propertyName, Relation::class);
    }

    public function getRelationById(string $propertyId): Relation
    {
        return $this->getTypedById($propertyId, Relation::class);
    }

    public function getRichText(string $propertyName): RichTextProperty
    {
        return $this->getTyped($propertyName, RichTextProperty::class);
    }

    public function getRichTextById(string $propertyId): RichTextProperty
    {
        return $this->getTypedById($propertyId, RichTextProperty::class);
    }

    public function getSelect(string $propertyName): Select
    {
        return $this->getTyped($propertyName, Select::class);
    }

    public function getSelectById(string $propertyId): Select
    {
        return $this->getTypedById($propertyId, Select::class);
    }

    public function getStatus(string $propertyName): Status
    {
        return $this->getTyped($propertyName, Status::class);
    }

    public function getStatusById(string $propertyId): Status
    {
        return $this->getTypedById($propertyId, Status::class);
    }

    public function getUniqueId(string $propertyName): UniqueId
    {
        return $this->getTyped($propertyName, UniqueId::class);
    }

    public function getUniqueIdById(string $propertyId): UniqueId
    {
        return $this->getTypedById($propertyId, UniqueId::class);
    }

    public function getUrl(string $propertyName): Url
    {
        return $this->getTyped($propertyName, Url::class);
    }

    public function getUrlById(string $propertyId): Url
    {
        return $this->getTypedById($propertyId, Url::class);
    }

    /**
     * @template T of PropertyInterface
     * @psalm-param class-string<T> $propertyType
     *
     * @psalm-return T
     */
    private function getTyped(string $propertyName, string $propertyType): PropertyInterface
    {
        $property = $this->get($propertyName);

        if ($property::class !== $propertyType) {
            throw new \TypeError("Property '{$propertyName}' is not of type {$propertyType}.");
        }

        /** @psalm-var T $property */
        return $property;
    }

    /**
     * @template T of PropertyInterface
     * @psalm-param class-string<T> $propertyType
     *
     * @psalm-return T
     */
    private function getTypedById(string $propertyId, string $propertyType): PropertyInterface
    {
        $property = $this->getById($propertyId);

        if ($property::class !== $propertyType) {
            throw new \TypeError("Property with ID '{$propertyId}' is not of type {$propertyType}.");
        }

        /** @psalm-var T $property */
        return $property;
    }
}
