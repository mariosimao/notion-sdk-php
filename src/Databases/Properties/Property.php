<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PropertyJson = array{ id: string, name: string, type: string }
 *
 * @psalm-immutable
 */
class Property
{
    public const TYPE_RICH_TEXT = "rich_text";
    public const TYPE_NUMBER = "number";
    public const TYPE_SELECT = "select";
    public const TYPE_MULTI_SELECT = "multi_select";
    public const TYPE_DATE = "date";
    public const TYPE_FORMULA = "formula";
    public const TYPE_RELATION = "relation";
    public const TYPE_ROLLUP = "rollup";
    public const TYPE_TITLE = "title";
    public const TYPE_PEOPLE = "people";
    public const TYPE_FILES = "files";
    public const TYPE_CHECKBOX = "checkbox";
    public const TYPE_URL = "url";
    public const TYPE_EMAIL = "email";
    public const TYPE_PHONE_NUMBER = "phone_number";
    public const TYPE_CREATED_TIME = "created_time";
    public const TYPE_CREATED_BY = "created_by";
    public const TYPE_LAST_EDITED_TIME = "last_edited_time";
    public const TYPE_LAST_EDITED_BY = "last_edited_by";

    private string $id;
    private string $name;
    private string $type;

    private function __construct(
        string $id,
        string $name,
        string $type,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    public static function create(string $id, string $name, string $type): self
    {
        return new self($id, $name, $type);
    }

    /**
     * @param PropertyJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["name"], $array["type"]);
    }

    public function toArray(): array
    {
        return [
            "id"   => $this->id,
            "name" => $this->name,
            "type" => $this->type,
        ];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isRichText(): bool
    {
        return $this->type === self::TYPE_RICH_TEXT;
    }

    public function isNumber(): bool
    {
        return $this->type === self::TYPE_NUMBER;
    }

    public function isSelect(): bool
    {
        return $this->type === self::TYPE_SELECT;
    }

    public function isMultiSelect(): bool
    {
        return $this->type === self::TYPE_MULTI_SELECT;
    }

    public function isDate(): bool
    {
        return $this->type === self::TYPE_DATE;
    }

    public function isFormula(): bool
    {
        return $this->type === self::TYPE_FORMULA;
    }

    // public function isRelation(): bool
    // {
    //     return $this->type === self::TYPE_RELATION;
    // }

    // public function isRollup(): bool
    // {
    //     return $this->type === self::TYPE_ROLLUP;
    // }

    public function isTitle(): bool
    {
        return $this->type === self::TYPE_TITLE;
    }

    public function isPeople(): bool
    {
        return $this->type === self::TYPE_PEOPLE;
    }

    public function isFiles(): bool
    {
        return $this->type === self::TYPE_FILES;
    }

    public function isCheckbox(): bool
    {
        return $this->type === self::TYPE_CHECKBOX;
    }

    public function isUrl(): bool
    {
        return $this->type === self::TYPE_URL;
    }

    public function isEmail(): bool
    {
        return $this->type === self::TYPE_EMAIL;
    }

    public function isPhoneNumber(): bool
    {
        return $this->type === self::TYPE_PHONE_NUMBER;
    }

    public function isCreatedTime(): bool
    {
        return $this->type === self::TYPE_CREATED_TIME;
    }

    public function isCreatedBy(): bool
    {
        return $this->type === self::TYPE_CREATED_BY;
    }

    public function isLastEditedTime(): bool
    {
        return $this->type === self::TYPE_LAST_EDITED_TIME;
    }

    public function isLastEditedBy(): bool
    {
        return $this->type === self::TYPE_LAST_EDITED_BY;
    }
}
