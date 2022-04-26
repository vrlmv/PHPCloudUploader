<?php
declare(strict_types=1);

namespace Vrlmv\PhpCloudUploader;

use Vrlmv\PhpCloudUploader\Collection\FileDTOCollection;
use Vrlmv\PhpCloudUploader\Exception\DeleteFileException;
use Vrlmv\PhpCloudUploader\Exception\DownloadFileException;
use Vrlmv\PhpCloudUploader\Exception\UploadFileException;

interface UploaderInterface
{
    /**
     * @throws UploadFileException
     */
    public function upload(UploadedFile $file): bool;

    /**
     * @throws DownloadFileException
     */
    public function download(string $filename, string $tmpPathname): string;

    /**
     * @throws DeleteFileException
     */
    public function delete($filename): bool;

    /**
     * @throws DeleteFileException
     */
    public function deleteMass(FileDTOCollection $filenames): void;

    /**
     * @throws DownloadFileException
     */
    public function getFileUrl($filename): string;
}
