<?php
declare(strict_types=1);

namespace Vrlmv\PhpCloudUploader;

use Vrlmv\PhpCloudUploader\Collection\FileDTOCollection;
use Vrlmv\PhpCloudUploader\Exception\DeleteFileException;
use Vrlmv\PhpCloudUploader\Exception\DownloadFileException;
use Vrlmv\PhpCloudUploader\Exception\UploadFileException;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;

class Uploader implements UploaderInterface
{
    private S3ClientInterface $client;

    public function __construct(
        private $username,
        private $password,
        private $bucket,
        private $endpoint,
        private $region,
    ) {
        $credentials = new Credentials($this->username, $this->password);

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'use_path_style_endpoint' => true,
            'endpoint' => $this->endpoint,
            'credentials' => $credentials,
        ]);
    }

    public function upload(UploadedFile $file): bool
    {
        if ($this->client->doesObjectExist($this->bucket, $file->getFilename())) {
            throw new UploadFileException("File already exists.");
        }

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $file->getFilename(),
            'Body' => $file->getContents()
        ]);

        return true;
    }

    public function download(string $filename, string $tmpPathname): string
    {
        if (!$this->client->doesObjectExist($this->bucket, $filename)) {
            throw new DownloadFileException("File does not exists.");
        }

        try {
            $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $filename,
                'SaveAs' => $tmpPathname,
            ]);
        } catch (AwsException $e) {
            throw new DownloadFileException('Unable to download a file.');
        }

        return $tmpPathname;
    }

    public function delete($filename): bool
    {
        try {
            if ($this->client->doesObjectExist($this->bucket, $filename)) {
                $this->client->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key' => $filename
                ]);
            }
        } catch (AwsException $exception) {
            throw new DeleteFileException('Error white deleting a file.');
        }

        return true;
    }

    public function getFileUrl($filename): string
    {
        if (!$this->client->doesObjectExist($this->bucket, $filename)) {
            throw new DownloadFileException("Object does not exists.");
        }

        $date = new \DateTime();
        $date->add(new \DateInterval('PT100M'));
        $command = $this->client->getCommand('getObject', ['Bucket' => $this->bucket, 'Key' => $filename]);

        return (string)$this->client->createPresignedRequest($command, $date->getTimestamp())->getUri();
    }

    public function deleteMass(FileDTOCollection $filenames): void
    {
        $objects = ['Objects' => []];

        foreach ($filenames->items() as $filename) {
            $objects['Objects'][] = ["Key" => $filename->key];
        }

        try {
            $this->client->deleteObjects([
                'Bucket' => $this->bucket,
                'Delete' => $objects,
            ]);
        } catch (AwsException $exception) {
            throw new DeleteFileException('Error while deleting a file.');
        }
    }
}
