<?php

namespace Notion\Blocks;

/**
 * Request to create AI meeting notes from an audio or video source.
 *
 * @psalm-type MeetingNotesRequestJson = array{
 *      source: array{ type: "file_upload", file_upload_id: string }|array{ type: "block", block_id: string },
 *      parent?: array{ type: "page_id", page_id: string },
 *      title?: string,
 *      language?: string,
 *      options?: array{ kickoff_summary: bool },
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesRequest
{
    private function __construct(
        public MeetingNotesSourceType $sourceType,
        public string $sourceId,
        public string|null $parentPageId,
        public string|null $title,
        public MeetingNotesLanguage|null $language,
        public bool|null $kickoffSummary,
    ) {
    }

    /**
     * Create meeting notes inside a page from a completed file upload.
     *
     * The file upload must have the `uploaded` status.
     */
    public static function fromFileUpload(string $fileUploadId, string $parentPageId): self
    {
        return new self(MeetingNotesSourceType::FileUpload, $fileUploadId, $parentPageId, null, null, null);
    }

    /**
     * Create meeting notes next to an existing audio, video or file block.
     */
    public static function fromBlock(string $blockId): self
    {
        return new self(MeetingNotesSourceType::Block, $blockId, null, null, null, null);
    }

    public function changeTitle(string $title): self
    {
        return new self(
            $this->sourceType,
            $this->sourceId,
            $this->parentPageId,
            $title,
            $this->language,
            $this->kickoffSummary,
        );
    }

    public function changeLanguage(MeetingNotesLanguage $language): self
    {
        return new self(
            $this->sourceType,
            $this->sourceId,
            $this->parentPageId,
            $this->title,
            $language,
            $this->kickoffSummary,
        );
    }

    /**
     * Start summary generation after transcription (Notion's default).
     */
    public function enableSummary(): self
    {
        return $this->changeKickoffSummary(true);
    }

    /**
     * Only transcribe the media, without generating a summary.
     */
    public function disableSummary(): self
    {
        return $this->changeKickoffSummary(false);
    }

    /** @psalm-return MeetingNotesRequestJson */
    public function toArray(): array
    {
        $array = [
            "source" => $this->sourceType === MeetingNotesSourceType::FileUpload
                ? [ "type" => "file_upload", "file_upload_id" => $this->sourceId ]
                : [ "type" => "block", "block_id" => $this->sourceId ],
        ];

        if ($this->parentPageId !== null) {
            $array["parent"] = [ "type" => "page_id", "page_id" => $this->parentPageId ];
        }

        if ($this->title !== null) {
            $array["title"] = $this->title;
        }

        if ($this->language !== null) {
            $array["language"] = $this->language->value;
        }

        if ($this->kickoffSummary !== null) {
            $array["options"] = [ "kickoff_summary" => $this->kickoffSummary ];
        }

        return $array;
    }

    private function changeKickoffSummary(bool $kickoffSummary): self
    {
        return new self(
            $this->sourceType,
            $this->sourceId,
            $this->parentPageId,
            $this->title,
            $this->language,
            $kickoffSummary,
        );
    }
}
