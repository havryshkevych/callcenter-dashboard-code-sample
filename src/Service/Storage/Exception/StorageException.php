<?php

declare(strict_types=1);

namespace App\Service\Storage\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

class StorageException extends \RuntimeException
{
    #[Pure]
    public static function putError(string $path, ?Exception $previous = null) : StorageException
    {
        return new self(sprintf('Could not put the given object at "%s".', $path), 0, $previous);
    }

    #[Pure]
    public static function getError(string $path, ?Exception $previous = null) : StorageException
    {
        return new self(sprintf('Could not get the given object at "%s".', $path), 0, $previous);
    }

    #[Pure]
    public static function deleteError(string $path, ?Exception $previous = null) : StorageException
    {
        return new self(sprintf('Could not remove the given object at "%s".', $path), 0, $previous);
    }
}