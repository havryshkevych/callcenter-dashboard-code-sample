<?php

namespace App\Service\Storage;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3ClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AmazonStorage implements StorageInterface
{
    const CACHE_TTN = 60*60*24*365;

    public function __construct(
        private S3ClientInterface $s3,
        private string $bucket
    )
    {
    }

    public function put(string $path, UploadedFile $data): void
    {
        try {
            $this->s3->upload($this->bucket, $path, $data->getContent(), 'private', [
                'params' => [
                    'ContentType' => $data->getMimeType(),
                    'CacheControl' => 'max-age='.self::CACHE_TTN
                ]
            ]);
        } catch (S3Exception $e) {
            throw Exception\StorageException::putError($path, $e);
        }
    }

    public function get(string $path): string
    {
        try {
            $model = $this->s3->getObject([
                'Bucket' => $this->bucket,
                'Key' => $path
            ]);

            return (string)$model->get('Body');
        } catch (S3Exception $e) {
            if ($e->getAwsErrorCode() === 'NoSuchKey') {
                throw Exception\NotFoundException::pathNotFound($path, $e);
            }

            throw Exception\StorageException::getError($path, $e);
        }
    }

    public function has(string $path): bool
    {
        return $this->s3->doesObjectExist($this->bucket, $path);
    }

    public function delete(string $path): void
    {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $path
            ]);
        } catch (S3Exception $e) {
            throw Exception\StorageException::deleteError($path, $e);
        }
    }
}