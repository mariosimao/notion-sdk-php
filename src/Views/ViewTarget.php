<?php

namespace Notion\Views;

/**
 * Where a new view is created
 *
 * @psalm-immutable
 */
final readonly class ViewTarget
{
    private function __construct(
        private array $payload,
    ) {
    }

    /** Add the view as a tab of an existing database */
    public static function database(string $databaseId, ViewPosition|null $position = null): self
    {
        $payload = [ "database_id" => $databaseId ];

        if ($position !== null) {
            $payload["position"] = $position->toArray();
        }

        return new self($payload);
    }

    /** Add the view as a widget of an existing dashboard view */
    public static function dashboard(string $dashboardViewId, WidgetPlacement|null $placement = null): self
    {
        $payload = [ "view_id" => $dashboardViewId ];

        if ($placement !== null) {
            $payload["placement"] = $placement->toArray();
        }

        return new self($payload);
    }

    /** Create a linked database block on a page, showing the view */
    public static function linkedDatabase(
        string $pageId,
        string|null $afterBlockId = null,
        bool|null $showDataSourceTitle = null,
    ): self {
        $createDatabase = [
            "parent" => [ "type" => "page_id", "page_id" => $pageId ],
        ];

        if ($afterBlockId !== null) {
            $createDatabase["position"] = [ "type" => "after_block", "block_id" => $afterBlockId ];
        }

        if ($showDataSourceTitle !== null) {
            $createDatabase["show_data_source_title"] = $showDataSourceTitle;
        }

        return new self([ "create_database" => $createDatabase ]);
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
