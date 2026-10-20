<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Expands the placeholders in an explicit operationId.
 *
 * `{method}` is replaced by the operation's method, as stored, in lowercase. Any other `{...}` is
 * left as written.
 *
 * @implements PipeInterface<Specification>
 */
class OperationIdTemplates implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->operations as $operation) {
            if ($operation->operationId !== null) {
                $operation->operationId = $this->expand($operation->operationId, $operation);
            }
        }

        return null;
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
}
