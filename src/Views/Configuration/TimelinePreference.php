<?php

namespace Notion\Views\Configuration;

/**
 * @psalm-type TimelinePreferenceJson = array{
 *      zoom_level: string,
 *      center_timestamp?: int,
 * }
 *
 * @psalm-immutable
 */
final readonly class TimelinePreference
{
    private function __construct(
        public TimelineZoomLevel $zoomLevel,
        public int|null $centerTimestamp,
    ) {
    }

    /** @param int|null $centerTimestamp Unix timestamp in milliseconds */
    public static function create(TimelineZoomLevel $zoomLevel, int|null $centerTimestamp = null): self
    {
        return new self($zoomLevel, $centerTimestamp);
    }

    /**
     * @param TimelinePreferenceJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(TimelineZoomLevel::from($array["zoom_level"]), $array["center_timestamp"] ?? null);
    }

    public function toArray(): array
    {
        $array = [ "zoom_level" => $this->zoomLevel->value ];

        if ($this->centerTimestamp !== null) {
            $array["center_timestamp"] = $this->centerTimestamp;
        }

        return $array;
    }
}
