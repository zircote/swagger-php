<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Logger decorator that collects entries while forwarding to a wrapped logger.
 */
class CollectingLogger extends AbstractLogger
{
    /** @var list<array{level: string, message: string}> */
    protected array $entries = [];

    public function __construct(protected LoggerInterface $delegate)
    {
    }

    public function log($level, $message, array $context = []): void
    {
        $this->entries[] = ['level' => (string) $level, 'message' => (string) $message];
        $this->delegate->log($level, $message, $context);
    }

    /**
     * @return list<array{level: string, message: string}>
     */
    public function entries(): array
    {
        return $this->entries;
    }
}
