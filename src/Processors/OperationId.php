<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Generate the OperationId based on the context of the OpenApi annotation.
 */
class OperationId
{
    protected bool $hash;

    public function __construct(bool $hash = true)
    {
        $this->hash = $hash;
    }

    public function isHash(): bool
    {
        return $this->hash;
    }

    /**
     *  If set to <code>true</code> generate ids (md5) instead of clear text operation ids.
     */
    public function setHash(bool $hash): OperationId
    {
        $this->hash = $hash;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
        $allOperations = $analysis->getAnnotationsOfType(OA\Operation::class);

        /** @var OA\Operation $operation */
        foreach ($allOperations as $operation) {
            if (null === $operation->operationId) {
                $operation->operationId = Generator::UNDEFINED;
            }

            if (!Generator::isDefault($operation->operationId)) {
                continue;
            }

            $context = $operation->_context;
            if ($context) {
                $source = $context->class ?? $context->interface ?? $context->trait;
                $operationId = null;
                if ($source) {
                    $method = $context->method ? ('::' . $context->method) : '';
                    $operationId = $context->namespace ? $context->namespace . '\\' . $source . $method : $source . $method;
                } elseif ($context->method) {
                    $operationId = $context->method;
                }

                if ($operationId) {
                    $operationId = strtoupper($operation->method) . '::' . $operation->path . '::' . $operationId;
                    $operation->operationId = $this->hash ? md5($operationId) : $operationId;
                }
            }
        }
    }
}
