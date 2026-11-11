<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Specification;

/**
 * Reads a versioned OpenAPI document into a Specification.
 */
interface ParserInterface
{
    /**
     * Whether this parser reads documents of the given version.
     */
    public function supports(string $version): bool;

    /**
     * @param array<string, mixed> $document the document as decoded from JSON or YAML, with maps as arrays
     */
    public function parse(array $document): Specification;
}
