<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Assembler;
use OpenApi\Contracts\ResolverInterface;

/**
 * Never resolves anything, but records every FQCN it was offered.
 *
 * A class-name resolver on purpose, as written before `KeyResolverInterface`: the chain still asks
 * one like it about keys without qualifiers.
 */
class CountingResolver implements ResolverInterface
{
    /**
     * @var array<string, int>
     */
    public array $attempts = [];

    public function resolve(string $fqcn, Assembler $assembler): bool
    {
        $this->attempts[$fqcn] = ($this->attempts[$fqcn] ?? 0) + 1;

        return false;
    }
}
