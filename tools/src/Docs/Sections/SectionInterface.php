<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

interface SectionInterface
{
    /**
     * @param array<string,mixed> $data Class-level structured data from the generator
     *
     * @return string Rendered markdown (empty string to skip)
     */
    public function render(array $data): string;
}
