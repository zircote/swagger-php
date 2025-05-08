<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Loggers;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ConsoleLogger extends AbstractLogger implements LoggerInterface
{
    public const COLOR_ERROR = "\033[31m";
    public const COLOR_WARNING = "\033[33m";
    public const COLOR_STOP = "\033[0m";

    private const LOG_LEVELS_UP_TO_NOTICE = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
    ];

    /** @var bool */
    protected $loggedMessageAboveNotice = false;

    protected bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function loggedMessageAboveNotice(): bool
    {
        return $this->loggedMessageAboveNotice;
    }

    /**
     * @param string            $level
     * @param string|\Exception $message
     * @param array             $context additional details; supports custom <code>prefix</code> and <code>exception</code>
     */
    public function log($level, $message, array $context = []): void
    {
        $prefix = '';
        $color = '';
        // level adjustments
        switch ($level) {
            case LogLevel::DEBUG:
                if (!$this->debug) {
                    return;
                }
                $prefix = 'Debug: ';
                // no break
            case LogLevel::WARNING:
                $prefix = $prefix ?: ($context['prefix'] ?? 'Warning: ');
                $color = static::COLOR_WARNING;
                break;
            case LogLevel::ERROR:
                $prefix = $context['prefix'] ?? 'Error: ';
                $color = static::COLOR_ERROR;
                break;
        }
        $stop = empty($color) ? '' : static::COLOR_STOP;

        if (!in_array($level, self::LOG_LEVELS_UP_TO_NOTICE, true)) {
            $this->loggedMessageAboveNotice = true;
        }

        /** @var ?\Exception $exception */
        $exception = $context['exception'] ?? null;
        if ($message instanceof \Exception) {
            $exception = $message;
            $message = $exception->getMessage();
        }

        $logLine = sprintf('%s%s%s%s', $color, $prefix, $message, $stop);
        error_log($logLine);

        if ($this->debug) {
            if ($exception) {
                error_log($exception->getTraceAsString());
            } elseif ($logLine !== '' && $logLine !== '0') {
                $stack = explode(PHP_EOL, (new \Exception())->getTraceAsString());
                // self
                array_shift($stack);
                // AbstractLogger
                array_shift($stack);
                foreach ($stack as $line) {
                    error_log($line);
                }
            }
        }
    }
}
