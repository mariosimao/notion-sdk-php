<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use Notion\Blocks\MeetingNotes\Query\Operator;
use Notion\Blocks\MeetingNotes\Query\Property;
use Notion\Blocks\MeetingNotes\Query\TextFilter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TextFilterTest extends TestCase
{
    public function test_default(): void
    {
        $filter = TextFilter::title();

        $this->assertSame(Property::Title, $filter->property());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertNull($filter->value());
    }

    #[DataProvider("valueOperators")]
    public function test_value_operators(TextFilter $filter, string $operator): void
    {
        $expected = [
            "property" => "title",
            "filter"   => [
                "operator" => $operator,
                "value"    => [ "type" => "exact", "value" => "sync" ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
        $this->assertSame("sync", $filter->value());
    }

    /** @return array<string, array{TextFilter, string}> */
    public static function valueOperators(): array
    {
        return [
            "equals"         => [ TextFilter::title()->equals("sync"), "string_is" ],
            "doesNotEqual"   => [ TextFilter::title()->doesNotEqual("sync"), "string_is_not" ],
            "contains"       => [ TextFilter::title()->contains("sync"), "string_contains" ],
            "doesNotContain" => [ TextFilter::title()->doesNotContain("sync"), "string_does_not_contain" ],
            "startsWith"     => [ TextFilter::title()->startsWith("sync"), "string_starts_with" ],
            "endsWith"       => [ TextFilter::title()->endsWith("sync"), "string_ends_with" ],
        ];
    }

    public function test_is_empty(): void
    {
        $this->assertSame(
            [ "property" => "title", "filter" => [ "operator" => "is_empty" ] ],
            TextFilter::title()->contains("a")->isEmpty()->toArray(),
        );
    }

    public function test_is_not_empty(): void
    {
        $this->assertSame(
            [ "property" => "title", "filter" => [ "operator" => "is_not_empty" ] ],
            TextFilter::title()->isNotEmpty()->toArray(),
        );
    }
}
