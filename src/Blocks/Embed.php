<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type EmbedJson = array{
 *      embed: array{
 *          url?: string,
 *          type?: "file_upload",
 *          file_upload?: array{ id: string },
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class Embed implements BlockInterface
{
    private function __construct(
        private BlockMetadata $metadata,
        public string|null $url,
        public string|null $fileUploadId,
    ) {
        $metadata->checkType(BlockType::Embed);
    }

    public static function fromUrl(string $url = ""): self
    {
        $metadata = BlockMetadata::create(BlockType::Embed);

        return new self($metadata, $url, null);
    }

    /**
     * Embed an uploaded file. Uploaded `.html`/`.htm` files are rendered by
     * Notion as HTML blocks.
     */
    public static function fromFileUpload(string $fileUploadId): self
    {
        $metadata = BlockMetadata::create(BlockType::Embed);

        return new self($metadata, null, $fileUploadId);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var EmbedJson $array */
        $embed = $array["embed"];

        return new self(
            $metadata,
            $embed["url"] ?? null,
            $embed["file_upload"]["id"] ?? null,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        if ($this->fileUploadId !== null) {
            $array["embed"] = [
                "type" => "file_upload",
                "file_upload" => [ "id" => $this->fileUploadId ],
            ];

            return $array;
        }

        $array["embed"] = [ "url" => $this->url ?? "" ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function isFileUpload(): bool
    {
        return $this->fileUploadId !== null;
    }

    public function changeUrl(string $url): self
    {
        return new self($this->metadata, $url, null);
    }

    /**
     * Attach an uploaded file to the embed. Uploaded `.html`/`.htm` files are
     * rendered by Notion as HTML blocks.
     */
    public function changeFileUpload(string $fileUploadId): self
    {
        return new self($this->metadata, null, $fileUploadId);
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
            $this->url,
            $this->fileUploadId,
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
