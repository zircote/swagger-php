<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\ValidatorInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @template T of OA\AbstractAnnotation
 * @implements ValidatorInterface<T>
 */
abstract class AbstractValidator implements ValidatorInterface, LoggerAwareInterface
{
    protected ?LoggerInterface $logger = null;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function version(Analysis $analysis): string
    {
        return $analysis->openapi->openapi ?? OA\OpenApi::DEFAULT_VERSION;
    }
}
