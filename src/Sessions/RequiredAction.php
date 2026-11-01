<?php

namespace Notion\Sessions;

/**
 * An action the agent is waiting on before it can continue.
 *
 * Answer it with `Notion\Sessions\Client::submitActions()`.
 *
 * @psalm-import-type RequiredActionOptionJson from RequiredActionOption
 *
 * @psalm-type RequiredActionJson = array{
 *      action_id: string,
 *      title: string,
 *      options: list<RequiredActionOptionJson>,
 * }
 *
 * @psalm-immutable
 */
final readonly class RequiredAction
{
    /** @param list<RequiredActionOption> $options */
    private function __construct(
        public string $actionId,
        public string $title,
        public array $options,
    ) {
    }

    /**
     * @param RequiredActionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["action_id"],
            $array["title"],
            array_map(RequiredActionOption::fromArray(...), $array["options"]),
        );
    }

    /**
     * @param list<RequiredActionJson> $array
     *
     * @return list<self>
     *
     * @internal
     */
    public static function fromArrayList(array $array): array
    {
        return array_map(self::fromArray(...), $array);
    }

    /** @return RequiredActionJson */
    public function toArray(): array
    {
        return [
            "action_id" => $this->actionId,
            "title" => $this->title,
            "options" => array_map(fn (RequiredActionOption $o) => $o->toArray(), $this->options),
        ];
    }
}
