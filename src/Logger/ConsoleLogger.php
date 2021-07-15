<?php

namespace OpenApi\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ConsoleLogger extends AbstractLogger implements LoggerInterface
{
    public const COLOR_ERROR = "\033[31m";
    public const COLOR_WARNING = "\033[33m";
    public const COLOR_STOP = "\033[0m";

    /** @var bool */
    protected $called = false;

    /** @var bool */
    protected $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function called()
    {
        return $this->called;
    }

    public function log($level, $message, array $context = [])
    {
        $this->called = true;

        $prefix = '';
        if (in_array($level, [LogLevel::NOTICE, LogLevel::INFO, LogLevel::DEBUG])) {
            $color = static::COLOR_WARNING;
        } else {
            $prefix = 'Warning: ';
            $color = static::COLOR_ERROR;
        }

        if ($message instanceof \Exception) {
            /** @var \Exception $e */
            $e = $message;
            error_log(static::COLOR_ERROR . 'Error: ' . $e->getMessage() . static::COLOR_STOP);
            if ($this->debug) {
                error_log('Stack trace:' . PHP_EOL . $e->getTraceAsString());
            }
        } else {
            error_log($color . $prefix . $message . static::COLOR_STOP);
            if ($this->debug) {
                // Show backtrace in debug mode
                $e = (string) (new \Exception('trace'));
                $trace = explode("\n", substr($e, strpos($e, 'Stack trace:')));
                foreach ($trace as $i => $entry) {
                    if ($i === 0) {
                        error_log($entry);
                    }
                    if ($i <= 3) {
                        continue;
                    }
                    preg_match('/#([0-9]+) (.*)$/', $entry, $match);
                    error_log('#' . ($match[1] - 2) . ' ' . $match[2]);
                }
            }
        }
    }
}
