<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Loggers;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Logger decorator that collects entries while forwarding to a wrapped logger.
 */
class CollectingLogger extends AbstractLogger implements LoggerAwareInterface
{
    /** @var list<array{level: string, message: string}> */
    protected array $entries = [];

    public function __construct(protected ?LoggerInterface $delegate = null)
    {
    }

    /**
     * Forwards from here on to the given logger; entries collected so far are kept.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->delegate = $logger;
    }

    public function getDelegate(): ?LoggerInterface
    {
        return $this->delegate;
    }

    /**
     * @param string|\Stringable $message
     */
    public function log($level, $message, array $context = []): void
    {
        $this->entries[] = ['level' => (string) $level, 'message' => (string) $message];
        $this->delegate?->log($level, $message, $context);
    }

    /**
     * @return list<array{level: string, message: string}>
     */
    public function entries(): array
    {
        return $this->entries;
    }

    /**
     * The entries collected so far, which are then cleared.
     *
     * @return list<array{level: string, message: string}>
     */
    public function drain(): array
    {
        $entries = $this->entries;
        $this->entries = [];

        return $entries;
    }
}
