<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;

/**
 * Generates operationId for operations that don't have one explicitly set.
 *
 * The id is derived from method, path and the declaring method, function or class. An
 * operation with no reflector is identified by method and path alone.
 *
 * An explicit operationId is a template: `{method}` is replaced by the operation's method, as
 * stored, in lowercase. Any other `{...}` is left as written.
 *
 * @implements PipeInterface<Specification>
 */
class OperationIds implements PipeInterface
{
    public function __construct(
        #[Config('If set to <code>true</code> generate ids (md5) instead of clear text operation ids.')]
        protected bool $hash = true,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->operations as $operation) {
            if ($operation->operationId !== null) {
                $operation->operationId = $this->expand($operation->operationId, $operation);
                continue;
            }

            $operationId = $this->generateId($operation);
            if ($operationId !== null) {
                $operation->operationId = $this->hash ? md5($operationId) : $operationId;
            }
        }

        return null;
    }

    public function setHash(bool $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    protected function expand(string $operationId, OA\Operation $operation): string
    {
        $placeholders = array_filter([
            '{method}' => $operation->method,
        ], static fn (?string $value): bool => $value !== null);

        return strtr($operationId, $placeholders);
    }

    protected function generateId(OA\Operation $operation): ?string
    {
        $reflector = $operation->getReflector();

        $source = match (true) {
            $reflector instanceof \ReflectionMethod => $operation->getSourceLocation()->qualifiedMethod(),
            $reflector instanceof \ReflectionFunction => $reflector->getName(),
            $reflector instanceof \ReflectionClass => $reflector->getName(),
            default => null,
        };

        $method = strtoupper($operation->method ?? 'GET');
        $path = $operation->path ?? '';

        if ($source === null) {
            return $path === '' ? null : $method . '::' . $path;
        }

        return $method . '::' . $path . '::' . $source;
    }
}
