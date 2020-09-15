<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Logger;
use Psr\Log\LoggerInterface;

class AbstractProcessor
{
    /** @var LoggerInterface A logger. */
    protected $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: Logger::psrInstance();
    }
}
