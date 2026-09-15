<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Doubles;

use Psr\Log\AbstractLogger;

/**
 * Collects the messages it is given, for assertions about what was logged.
 *
 * `$message` is deliberately untyped: `psr/log` only added `string|\Stringable` to
 * `LoggerInterface::log()` in 2.0, and this package still supports `^1.1`, where declaring
 * it is a signature incompatibility.
 */
final class RecordingLogger extends AbstractLogger
{
    /**
     * @param list<string> $received
     */
    public function __construct(public array &$received)
    {
    }

    /**
     * @param mixed              $level
     * @param string|\Stringable $message
     * @param array<mixed>       $context
     */
    public function log($level, $message, array $context = []): void
    {
        $this->received[] = (string) $message;
    }
}
