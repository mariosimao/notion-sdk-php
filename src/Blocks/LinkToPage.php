<?php

namespace Notion\Blocks;

use Exception;
use Notion\Exceptions\BlockException;

/**
 * Link to page block
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type LinkToPageJson = array{
 *      link_to_page: array{
 *          type?: "page_id"|"database_id"|"comment_id",
 *          page_id?: string,
 *          database_id?: string,
 *          comment_id?: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class LinkToPage implements BlockInterface
{
    private function __construct(
        private BlockMetadata $metadata,
        public LinkToPageType $type,
        public string|null $pageId = null,
        public string|null $databaseId = null,
        public string|null $commentId = null,
    ) {
        $metadata->checkType(BlockType::LinkToPage);
    }

    public static function create(string $id, LinkToPageType $type = LinkToPageType::Page): self
    {
        return match ($type) {
            LinkToPageType::Page => self::page($id),
            LinkToPageType::Database => self::database($id),
            LinkToPageType::Comment => self::comment($id),
        };
    }

    public static function page(string $pageId): self
    {
        $metadata = BlockMetadata::create(BlockType::LinkToPage);

        return new self($metadata, LinkToPageType::Page, pageId: $pageId);
    }

    public static function database(string $databaseId): self
    {
        $metadata = BlockMetadata::create(BlockType::LinkToPage);

        return new self($metadata, LinkToPageType::Database, databaseId: $databaseId);
    }

    public static function comment(string $commentId): self
    {
        $metadata = BlockMetadata::create(BlockType::LinkToPage);

        return new self($metadata, LinkToPageType::Comment, commentId: $commentId);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var LinkToPageJson $array */
        $data = $array["link_to_page"];

        $typeString = $data["type"] ?? null;
        $pageId = $data["page_id"] ?? null;
        $databaseId = $data["database_id"] ?? null;
        $commentId = $data["comment_id"] ?? null;

        if ($typeString !== null) {
            $type = LinkToPageType::from($typeString);
        } elseif ($pageId !== null) {
            $type = LinkToPageType::Page;
        } elseif ($databaseId !== null) {
            $type = LinkToPageType::Database;
        } elseif ($commentId !== null) {
            $type = LinkToPageType::Comment;
        } else {
            throw new Exception("Invalid link_to_page block array.");
        }

        return new self(
            $metadata,
            $type,
            $pageId,
            $databaseId,
            $commentId,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $link = [
            "type" => $this->type->value,
        ];

        if ($this->isPage()) {
            $link["page_id"] = $this->pageId;
        }

        if ($this->isDatabase()) {
            $link["database_id"] = $this->databaseId;
        }

        if ($this->isComment()) {
            $link["comment_id"] = $this->commentId;
        }

        $array["link_to_page"] = $link;

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    /**
     * Returns the target ID (page, database, or comment ID).
     */
    public function targetId(): string
    {
        return $this->pageId ?? $this->databaseId ?? $this->commentId ?? "";
    }

    /**
     * @psalm-assert-if-true string $this->pageId
     */
    public function isPage(): bool
    {
        return $this->type === LinkToPageType::Page;
    }

    /**
     * @psalm-assert-if-true string $this->databaseId
     */
    public function isDatabase(): bool
    {
        return $this->type === LinkToPageType::Database;
    }

    /**
     * @psalm-assert-if-true string $this->commentId
     */
    public function isComment(): bool
    {
        return $this->type === LinkToPageType::Comment;
    }

    public function changePage(string $pageId): self
    {
        return new self(
            $this->metadata,
            LinkToPageType::Page,
            pageId: $pageId,
        );
    }

    public function changeDatabase(string $databaseId): self
    {
        return new self(
            $this->metadata,
            LinkToPageType::Database,
            databaseId: $databaseId,
        );
    }

    public function changeComment(string $commentId): self
    {
        return new self(
            $this->metadata,
            LinkToPageType::Comment,
            commentId: $commentId,
        );
    }

    public function addChild(BlockInterface $child): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->type,
            $this->pageId,
            $this->databaseId,
            $this->commentId,
        );
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
