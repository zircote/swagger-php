<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Builder;

/**
 * A controller carrying no spec attributes, so nothing here is scanned.
 */
class PlainController
{
    /**
     * List the things.
     *
     * Returns everything, paged.
     *
     * @return array<string, mixed>
     */
    public function index(int $page, ?string $filter = null): array
    {
        return [];
    }
}
