<?php
declare(strict_types=1);

namespace Vrlmv\PhpCloudUploader;

class UploadedFile implements UploadedFileInterface
{
    public function __construct(
        private string $filename,
        private string $fileMimeType,
        private string $contents,
    ) {
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
