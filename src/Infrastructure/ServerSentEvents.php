<?php

namespace Notion\Infrastructure;

use Generator;
use Psr\Http\Message\StreamInterface;

/**
 * Parser for `text/event-stream` responses.
 *
 * @see https://html.spec.whatwg.org/multipage/server-sent-events.html#event-stream-interpretation
 *
 * @psalm-type ServerSentEvent = array{ event: string|null, data: string, id: string|null }
 *
 * @internal
 */
final class ServerSentEvents
{
    private const CHUNK_SIZE = 8192;

    /**
     * Read events from a stream as soon as they are available.
     *
     * @return Generator<int, ServerSentEvent, mixed, void>
     */
    public static function parse(StreamInterface $stream): Generator
    {
        $buffer = "";

        while (!$stream->eof()) {
            $chunk = $stream->read(self::CHUNK_SIZE);
            if ($chunk === "") {
                continue;
            }

            $buffer .= $chunk;

            // A "\r" at the end of the buffer may be the first half of "\r\n"
            $pending = str_ends_with($buffer, "\r") ? "\r" : "";
            if ($pending !== "") {
                $buffer = substr($buffer, 0, -1);
            }
            $buffer = str_replace(["\r\n", "\r"], "\n", $buffer);

            while (($position = strpos($buffer, "\n\n")) !== false) {
                $block = substr($buffer, 0, $position);
                $buffer = substr($buffer, $position + 2);

                $event = self::parseBlock($block);
                if ($event !== null) {
                    yield $event;
                }
            }

            $buffer .= $pending;
        }

        $event = self::parseBlock(str_replace(["\r\n", "\r"], "\n", $buffer));
        if ($event !== null) {
            yield $event;
        }
    }

    /** @return ServerSentEvent|null */
    private static function parseBlock(string $block): array|null
    {
        $event = null;
        $id = null;
        $data = [];

        foreach (explode("\n", $block) as $line) {
            if ($line === "" || str_starts_with($line, ":")) {
                continue;
            }

            $parts = explode(":", $line, 2);
            $field = $parts[0];
            $value = $parts[1] ?? "";
            if (str_starts_with($value, " ")) {
                $value = substr($value, 1);
            }

            match ($field) {
                "event" => $event = $value,
                "data" => $data[] = $value,
                "id" => $id = $value,
                default => null,
            };
        }

        if ($data === []) {
            return null;
        }

        return [
            "event" => $event,
            "data" => implode("\n", $data),
            "id" => $id,
        ];
    }
}
