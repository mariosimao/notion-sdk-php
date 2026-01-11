<?php

namespace Notion\Exceptions;

class FileUploadException extends NotionException
{
    public static function fileDoesNotExist(string $filePath): self
    {
        return new self("File {$filePath} does not exist.");
    }

    public static function fileIsNotReadable(string $filePath): self
    {
        return new self("File {$filePath} is not readable.");
    }

    public static function fileSizeCouldNotBeDetermined(string $filePath): self
    {
        return new self("The size of file {$filePath} could not be determined.");
    }

    public static function couldNotGetFileContent(string $filePath): self
    {
        return new self("Could not get the contents of file {$filePath}.");
    }

    public static function couldNotOpenFileForReading(string $filePath): self
    {
        return new self("Could not open file {$filePath} for reading.");
    }

    public static function couldNotReadChunkFromFile(string $filePath, int $partNumber): self
    {
        return new self("Could not read chunk {$partNumber} from file {$filePath}.");
    }
}
