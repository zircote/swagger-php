<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Doubles;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Appends every entry to the given list, then forwards it to an optional delegate.
 *
 * Holds the list by reference rather than a recorder closure: `Context::__serialize()` drops
 * anonymous classes but keeps named ones, so a closure property here makes any Context
 * carrying this logger unserializable.
 *
 * `$message` is deliberately untyped: `psr/log` only added `string|\Stringable` to
 * `LoggerInterface::log()` in 2.0, and this package still supports `^1.1`, where declaring
 * it is a signature incompatibility.
 */
final class ForwardingLogger extends AbstractLogger
{
    /**
     * @param list<array{level: string, message: string}> $entries
     */
    public function __construct(protected array &$entries, protected ?LoggerInterface $delegate)
    {
    }

    /**
     * @param mixed              $level
     * @param string|\Stringable $message
     * @param array<mixed>       $context
     */
    public function log($level, $message, array $context = []): void
    {
        $this->entries[] = ['level' => (string) $level, 'message' => (string) $message];

        $this->delegate?->log($level, $message, $context);
    }
}
