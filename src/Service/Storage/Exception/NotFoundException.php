<?php declare(strict_types=1);

namespace App\Service\Storage\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

class NotFoundException extends StorageException
{
    #[Pure]
    public static function pathNotFound(string $path, ?Exception $previous = null) : NotFoundException
    {
        return new self(sprintf('The path "%s" does not exist', $path), 0, $previous);
    }
}