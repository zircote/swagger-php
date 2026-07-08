<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\SourceLocation;

class OpenApiException extends \Exception
{
    protected SourceLocation $sourceLocation;

    public static function fromSource(string $message, SourceLocation $sourceLocation, ?\Throwable $previous = null): self
    {
        $exception = new self("{$message} at {$sourceLocation}", 0, $previous);
        $exception->sourceLocation = $sourceLocation;

        return $exception;
    }

    public function getSourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }
}
