<?php declare(strict_types=1);

namespace App\Service\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageInterface
{
    /**
     * @param string $path The object path.
     * @param UploadedFile $data The object data.
     * @return void
     * @throws Exception\StorageException If an error occurs.
     */
    public function put(string $path, UploadedFile $data) : void;

    /**
     * @param string $path The object path.
     * @return string The object data.
     * @throws Exception\NotFoundException If the path is not found.
     * @throws Exception\StorageException  If an error occurs.
     */
    public function get(string $path) : string;

    /**
     * @param string $path
     * @return bool
     * @throws Exception\StorageException If an error occurs.
     */
    public function has(string $path) : bool;

    /**
     * @param string $path
     * @return void
     * @throws Exception\StorageException If an error occurs.
     */
    public function delete(string $path) : void;
}