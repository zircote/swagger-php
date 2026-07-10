<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Optional interface for pipeline pipes that support grouping.
 *
 * Pipes without this interface are placed in the pipeline's default group.
 */
interface PipeInterface
{
    /**
     * The group this pipe belongs to.
     */
    public function group(): string|\BackedEnum;

    public function __invoke(mixed $payload): mixed;
}
