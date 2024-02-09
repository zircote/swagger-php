<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Loggers;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class DefaultLogger extends AbstractLogger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        if (LogLevel::DEBUG == $level) {
            return;
        }

        $error_level = in_array($level, [LogLevel::NOTICE, LogLevel::INFO]) ? E_USER_NOTICE : E_USER_WARNING;

        trigger_error($message, $error_level);
    }
}
