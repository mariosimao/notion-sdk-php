<?php

namespace Notion\FileUploads;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Notion\Configuration;
use Notion\Exceptions\FileUploadException;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type FileUploadJson from FileUpload
 */
class Client
{
    const SINGLE_PART_MAX_SIZE = 20 * 1024 * 1024; // 20 MB
    const CHUNK_SIZE = 10 * 1024 * 1024; // 10 MB
    const MAX_PAGE_SIZE = 100;

    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private readonly Configuration $config,
    ) {
    }

    public function find(string $fileUploadId): FileUpload
    {
        $url = "https://api.notion.com/v1/file_uploads/{$fileUploadId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("GET");

        /** @psalm-var FileUploadJson $body */
        $body = Http::sendRequest($request, $this->config);

        return FileUpload::fromArray($body);
    }

    /**
     * @return array{
     *      results: FileUpload[],
     *      nextCursor: string|null,
     *      hasMore: bool,
     * }
     */
    public function list(FileUploadStatus|null $status = null, string|null $cursor = null): array
    {
        $uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $uri = $uriFactory->createUri("https://api.notion.com/v1/file_uploads");

        $queryParams = [ "page_size" => self::MAX_PAGE_SIZE ];
        if ($status !== null) {
            $queryParams["status"] = $status->value;
        }
        if ($cursor !== null) {
            $queryParams["start_cursor"] = $cursor;
        }
        $uri->withQuery(http_build_query($queryParams));

        $request = Http::createRequest((string) $uri, $this->config)
            ->withMethod("GET");

        /** @psalm-var array{results: FileUploadJson[], next_cursor: string|null, has_more: bool} $body */
        $body = Http::sendRequest($request, $this->config);

        $results = array_map(
            fn (array $item): FileUpload => FileUpload::fromArray($item),
            $body["results"]
        );
        return [
            "results" => $results,
            "nextCursor" => $body["next_cursor"],
            "hasMore" => $body["has_more"],
        ];
    }

    /**
     * @return FileUpload[]
     */
    public function findAll(FileUploadStatus|null $status = null): array
    {
        $fileUploads = [];
        $startCursor = null;
        $hasMore = true;

        while ($hasMore) {
            $result = $this->list($status, $startCursor);

            $fileUploads = array_merge($fileUploads, $result["results"]);
            $hasMore = $result["hasMore"];
            $startCursor = $result["nextCursor"];
        }

        return $fileUploads;
    }

    public function upload(string $filePath, string|null $filenameOnNotion = null): FileUpload
    {
        if (!file_exists($filePath)) {
            throw FileUploadException::fileDoesNotExist($filePath);
        }

        if (!is_readable($filePath)) {
            throw FileUploadException::fileIsNotReadable($filePath);
        }

        $filename = $filenameOnNotion ?? basename($filePath);

        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw FileUploadException::fileSizeCouldNotBeDetermined($filePath);
        }

        if ($fileSize < self::SINGLE_PART_MAX_SIZE) {
            $fileUpload = $this->createSinglePart();

            $content = file_get_contents($filePath);
            if ($content === false) {
                throw FileUploadException::couldNotGetFileContent($filePath);
            }

            $fileUpload = $this->send($fileUpload->id, $content, $filename, null);
            return $fileUpload;
        }

        $contentType = mime_content_type($filePath) ?: "application/octet-stream";
        $numberOfParts = (int) ceil($fileSize / self::CHUNK_SIZE);
        $fileUpload = $this->createMultiPart($filename, $contentType, $numberOfParts);
        foreach ($this->chunksGenerator($filePath) as $partNumber => $chunk) {
            $this->sendMultiPart($fileUpload->id, $chunk, $filename, $partNumber);
        }

        $fileUpload = $this->complete($fileUpload->id);

        return $fileUpload;
    }

    public function createSinglePart(string|null $filename = null): FileUpload
    {
        $body = [
            "mode" => Mode::SinglePart->value,
        ];
        if ($filename !== null) {
            $body["filename"] = $filename;
        }

        return $this->create($body);
    }

    public function createMultiPart(string $filename, string $contentType, int $numberOfParts): FileUpload
    {
        $body = [
            "mode" => Mode::MultiPart->value,
            "filename" => $filename,
            "content_type" => $contentType,
            "number_of_parts" => $numberOfParts,
        ];

        return $this->create($body);
    }

    public function createExternalUrl(string $filename, string $externalUrl): FileUpload
    {
        $body = [
            "mode" => Mode::ExternalUrl->value,
            "filename" => $filename,
            "external_url" => $externalUrl,
        ];

        return $this->create($body);
    }

    public function sendSinglePart(string $fileUploadId, string $filename, string $content): FileUpload
    {
        return $this->send($fileUploadId, $content, $filename, null);
    }

    public function sendMultiPart(string $fileUploadId, string $content, string $filename, int $partNumber) : FileUpload
    {
        return $this->send($fileUploadId, $content, $filename, $partNumber);
    }

    public function complete(string $fileUploadId): FileUpload
    {
        $url = "https://api.notion.com/v1/file_uploads/{$fileUploadId}/complete";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST");

        /** @psalm-var FileUploadJson $body */
        $body = Http::sendRequest($request, $this->config);

        return FileUpload::fromArray($body);
    }

    private function create(array $requestBody): FileUpload
    {
        $data = json_encode($requestBody);

        $url = "https://api.notion.com/v1/file_uploads";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var FileUploadJson $responseBody */
        $responseBody = Http::sendRequest($request, $this->config);

        return FileUpload::fromArray($responseBody);
    }

    private function send(
        string $fileUploadId,
        string $content,
        string $filename,
        int|null $partNumber
    ): FileUpload {
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $builder = new MultipartStreamBuilder($streamFactory);

        $builder->addResource("file", $content, [ "filename" => $filename ]);

        if ($partNumber !== null) {
            $builder->addResource("part_number", (string) $partNumber);
        }

        $multipartStream = $builder->build();
        $boundary = $builder->getBoundary();

        $url = "https://api.notion.com/v1/file_uploads/{$fileUploadId}/send";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "multipart/form-data; boundary={$boundary}")
            ->withBody($multipartStream);

        /** @psalm-var FileUploadJson $body */
        $body = Http::sendRequest($request, $this->config);

        return FileUpload::fromArray($body);
    }

    /**
     * @return \Generator<int, string>
     */
    private function chunksGenerator(string $filePath): \Generator
    {
        $handle = fopen($filePath, "rb");
        if ($handle === false) {
            throw FileUploadException::couldNotOpenFileForReading($filePath);
        }

        $partNumber = 1;
        while (!feof($handle)) {
            $buffer = fread($handle, self::CHUNK_SIZE);
            if ($buffer === false) {
                throw FileUploadException::couldNotReadChunkFromFile($filePath, $partNumber);
            }

            yield $partNumber => $buffer;
            $partNumber++;
        }

        fclose($handle);
    }
}
