<?php

namespace Notion\Views\Configuration;

/**
 * Visibility and display settings of a property in a view
 *
 * @psalm-type PropertyConfigurationJson = array{
 *      property_id: string,
 *      property_name?: string,
 *      visible?: bool,
 *      width?: int,
 *      wrap?: bool,
 *      status_show_as?: string,
 *      card_property_width_mode?: string,
 *      date_format?: string,
 *      time_format?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class PropertyConfiguration
{
    private function __construct(
        public string $propertyId,
        public string|null $propertyName,
        public bool|null $visible,
        public int|null $width,
        public bool|null $wrap,
        public StatusShowAs|null $statusShowAs,
        public CardPropertyWidthMode|null $cardPropertyWidthMode,
        public DateFormat|null $dateFormat,
        public TimeFormat|null $timeFormat,
    ) {
    }

    /** @param string $propertyId Property ID or property name */
    public static function create(string $propertyId): self
    {
        return new self($propertyId, null, null, null, null, null, null, null, null);
    }

    /**
     * @param PropertyConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["property_id"],
            $array["property_name"] ?? null,
            $array["visible"] ?? null,
            $array["width"] ?? null,
            $array["wrap"] ?? null,
            isset($array["status_show_as"]) ? StatusShowAs::from($array["status_show_as"]) : null,
            isset($array["card_property_width_mode"])
                ? CardPropertyWidthMode::from($array["card_property_width_mode"])
                : null,
            isset($array["date_format"]) ? DateFormat::from($array["date_format"]) : null,
            isset($array["time_format"]) ? TimeFormat::from($array["time_format"]) : null,
        );
    }

    public function toArray(): array
    {
        $array = [ "property_id" => $this->propertyId ];

        if ($this->visible !== null) {
            $array["visible"] = $this->visible;
        }
        if ($this->width !== null) {
            $array["width"] = $this->width;
        }
        if ($this->wrap !== null) {
            $array["wrap"] = $this->wrap;
        }
        if ($this->statusShowAs !== null) {
            $array["status_show_as"] = $this->statusShowAs->value;
        }
        if ($this->cardPropertyWidthMode !== null) {
            $array["card_property_width_mode"] = $this->cardPropertyWidthMode->value;
        }
        if ($this->dateFormat !== null) {
            $array["date_format"] = $this->dateFormat->value;
        }
        if ($this->timeFormat !== null) {
            $array["time_format"] = $this->timeFormat->value;
        }

        return $array;
    }

    public function show(): self
    {
        return $this->with(visible: true);
    }

    public function hide(): self
    {
        return $this->with(visible: false);
    }

    public function changeWidth(int $width): self
    {
        return $this->with(width: $width);
    }

    public function changeWrap(bool $wrap): self
    {
        return $this->with(wrap: $wrap);
    }

    public function changeStatusShowAs(StatusShowAs $statusShowAs): self
    {
        return $this->with(statusShowAs: $statusShowAs);
    }

    public function changeCardPropertyWidthMode(CardPropertyWidthMode $mode): self
    {
        return $this->with(cardPropertyWidthMode: $mode);
    }

    public function changeDateFormat(DateFormat $dateFormat): self
    {
        return $this->with(dateFormat: $dateFormat);
    }

    public function changeTimeFormat(TimeFormat $timeFormat): self
    {
        return $this->with(timeFormat: $timeFormat);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
