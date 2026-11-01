<?php

namespace Notion\Common;

/**
 * @psalm-type ParentJson = array{
 *      type: string,
 *      database_id?: string,
 *      page_id?: string,
 *      block_id?: string,
 *      workspace?: true,
 *      agent_id?: string,
 * }
 */
final readonly class ParentBlock
{
    private function __construct(
        public ParentType $type,
        public string|null $id
    ) {
    }

    public static function database(string $databaseId): self
    {
        return new self(ParentType::Database, $databaseId);
    }

    public static function page(string $pageId): self
    {
        return new self(ParentType::Page, $pageId);
    }

    public static function block(string $blockId): self
    {
        return new self(ParentType::Block, $blockId);
    }

    public static function workspace(): self
    {
        return new self(ParentType::Workspace, null);
    }

    /**
     * Agent parents are only returned by the API for blocks inside agent instruction pages.
     */
    public static function agent(string $agentId): self
    {
        return new self(ParentType::Agent, $agentId);
    }

    /** @psalm-param ParentJson $array */
    public static function fromArray(array $array): self
    {
        $type = ParentType::from($array["type"]);

        $id = match ($type) {
            ParentType::Database => $array["database_id"] ?? "",
            ParentType::Page => $array["page_id"] ?? "",
            ParentType::Block => $array["block_id"] ?? "",
            ParentType::Workspace => null,
            ParentType::Agent => $array["agent_id"] ?? "",
        };

        return new self($type, $id);
    }

    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
        ];

        if ($this->type === ParentType::Database) {
            $array["database_id"] = $this->id;
        }

        if ($this->type === ParentType::Block) {
            $array["block_id"] = $this->id;
        }

        if ($this->type === ParentType::Page) {
            $array["page_id"] = $this->id;
        }

        if ($this->type === ParentType::Workspace) {
            $array["workspace"] = true;
        }

        if ($this->type === ParentType::Agent) {
            $array["agent_id"] = $this->id;
        }

        return $array;
    }
}
