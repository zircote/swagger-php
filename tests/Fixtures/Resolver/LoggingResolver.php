<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Assembler;
use OpenApi\Contracts\ResolverInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Never resolves anything, and says so for every FQCN it was offered.
 */
class LoggingResolver implements ResolverInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function resolve(string $fqcn, Assembler $assembler): bool
    {
        $this->logger?->warning('Declined ' . $fqcn);

        return false;
    }
}
