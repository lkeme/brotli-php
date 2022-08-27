<?php
declare(strict_types=1);

namespace HelloNico\Brotli\Exception;

final class InvalidBinaryException extends \InvalidArgumentException implements BrotliException
{
    public static function create(string $filepath): self
    {
        return new self(sprintf('No binary %s available for your system.', $filepath));
    }
}
