<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

/**
 * Optional interface for pipeline pipes that support grouping.
 *
 * Pipes without this interface are placed in the pipeline's default group.
 *
 * @template T
 */
interface PipeInterface
{
    /**
     * The group this pipe belongs to.
     */
    public function group(): string|\BackedEnum;

    /**
     * @param T $payload
     *
     * @return T|null
     */
    public function __invoke(mixed $payload): mixed;
}
