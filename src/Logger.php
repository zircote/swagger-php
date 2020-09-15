<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use Exception;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Logger reports the parser and validation messages.
 */
class Logger
{
    /**
     * Singleton.
     *
     * @var Logger
     */
    public static $instance;

    /**
     * @var callable|LoggerInterface
     */
    public $log;

    protected function __construct()
    {
        /*
         * @param \Exception|string $entry
         * @param int $type Error type
         */
        $this->log = function ($entry, $type) {
            if ($entry instanceof Exception) {
                $entry = $entry->getMessage();
            }
            trigger_error($entry, $type);
        };
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public static function psrInstance(): LoggerInterface
    {
        return new class() extends AbstractLogger {
            public function log($level, $message, array $context = [])
            {
                // BC: delegate to the static instance
                if (in_array($level, [LogLevel::NOTICE, LogLevel::INFO, LogLevel::DEBUG])) {
                    Logger::notice($message);
                } else {
                    Logger::warning($message);
                }
            }
        };
    }

    /**
     * Log an OpenApi warning.
     *
     * @param Exception|string $entry
     */
    public static function warning($entry): void
    {
        call_user_func(self::getInstance()->log, $entry, E_USER_WARNING);
    }

    /**
     * Log an OpenApi notice.
     *
     * @param Exception|string $entry
     */
    public static function notice($entry): void
    {
        call_user_func(self::getInstance()->log, $entry, E_USER_NOTICE);
    }

    /**
     * Shorten class name(s).
     *
     * @param array|object|string $classes Class(es) to shorten
     *
     * @return string|string[] One or more shortened class names
     */
    public static function shorten($classes)
    {
        $short = [];
        foreach ((array) $classes as $class) {
            $short[] = '@'.str_replace('OpenApi\\Annotations\\', 'OA\\', $class);
        }

        return is_array($classes) ? $short : array_pop($short);
    }
}
