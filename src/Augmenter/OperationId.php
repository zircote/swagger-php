<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\AugmenterInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Generates operationId for operations that don't have one explicitly set.
 */
class OperationId implements AugmenterInterface
{
    /**
     * @param bool $hash If set to true, generate ids (md5) instead of clear text operation ids
     */
    public function __construct(
        protected bool $hash = true,
    ) {
    }

    public function setHash(bool $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function __invoke(Specification $specification): void
    {
        foreach ($specification->operations as $operation) {
            if ($operation->operationId !== null) {
                continue;
            }

            $operationId = $this->generateId($operation);
            if ($operationId !== null) {
                $operation->operationId = $this->hash ? md5($operationId) : $operationId;
            }
        }
    }

    protected function generateId(OA\Operation $operation): ?string
    {
        $reflector = $operation->getReflector();

        $source = null;
        if ($reflector instanceof \ReflectionMethod) {
            $source = $reflector->getDeclaringClass()->getName() . '::' . $reflector->getName();
        } elseif ($reflector instanceof \ReflectionFunction) {
            $source = $reflector->getName();
        }

        if ($source === null) {
            return null;
        }

        $method = strtoupper($operation->method ?? 'GET');
        $path = $operation->path ?? '';

        return $method . '::' . $path . '::' . $source;
    }
}
