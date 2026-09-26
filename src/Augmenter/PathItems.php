<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\PathItemHierarchy;
use OpenApi\Utils\PipeInterface;

/**
 * Resolves PathItem prefixes, clones metadata to operations, and sets path-level output.
 *
 * Composes path prefixes from the PathItems governing each operation's class, prepends them
 * to operation paths, clones tags/security/responses to operations that don't declare their
 * own, and marks PathItems that have spec-level output (parameters, summary, description,
 * servers) with their resolved path.
 *
 * The ancestor walk itself belongs to `Specification\PathItemHierarchy`.
 *
 * @implements PipeInterface<Specification>
 */
class PathItems implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $hierarchy = $payload->buildPathItemHierarchy();

        if ($hierarchy->classes() === []) {
            return null;
        }

        foreach ($payload->operations as $operation) {
            $chain = $hierarchy->forOperation($operation);
            if ($chain === []) {
                continue;
            }

            $prefix = $this->resolvePrefix($chain);
            if ($prefix !== '' && $operation->path !== null) {
                $operationPath = ltrim($operation->path, '/');
                $operation->path = $operationPath !== '' ? $prefix . '/' . $operationPath : $prefix;
            }

            $this->cloneMetadata($chain, $operation);
        }

        $this->resolvePathItemPaths($payload, $hierarchy);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * Compose the path prefix declared across a chain, outermost first.
     *
     * @param list<OA\PathItem> $chain
     */
    protected function resolvePrefix(array $chain): string
    {
        $parts = [];
        foreach ($chain as $pathItem) {
            if ($pathItem->prefix !== null && ($part = trim($pathItem->prefix, '/')) !== '') {
                $parts[] = $part;
            }
        }

        return $parts !== [] ? '/' . implode('/', $parts) : '';
    }

    /**
     * @param list<OA\PathItem> $chain
     */
    protected function cloneMetadata(array $chain, OA\Operation $operation): void
    {
        $merged = $this->collectMergedMetadata($chain);

        if ($merged['tags'] !== null) {
            $operation->tags = array_values(array_unique([...$operation->tags ?? [], ...$merged['tags']]));
        }

        if ($merged['security'] !== null) {
            $existingSchemes = [];
            foreach ($operation->security ?? [] as $req) {
                $existingSchemes[$req->scheme] = true;
            }
            foreach ($merged['security'] as $req) {
                if (!isset($existingSchemes[$req->scheme])) {
                    $operation->security ??= [];
                    $operation->security[] = $req;
                }
            }
        }

        if ($merged['responses'] !== null) {
            $existingCodes = [];
            foreach ($operation->responses ?? [] as $response) {
                if ($response->response !== null) {
                    $existingCodes[(string) $response->response] = true;
                }
            }

            foreach ($merged['responses'] as $response) {
                if ($response->response !== null && !isset($existingCodes[(string) $response->response])) {
                    $operation->responses ??= [];
                    $operation->responses[] = $response;
                }
            }
        }
    }

    /**
     * Collect merged metadata across a chain.
     * All collections merge additively — tags, security, and responses accumulate from all ancestors.
     *
     * @param  list<OA\PathItem>                                                                                               $chain
     * @return array{tags: list<string>|null, security: list<OA\Security\Requirement>|null, responses: list<OA\Response>|null}
     */
    protected function collectMergedMetadata(array $chain): array
    {
        $tags = [];
        $security = [];
        $responses = [];

        foreach ($chain as $pathItem) {
            if ($pathItem->tags !== null) {
                array_push($tags, ...$pathItem->tags);
            }
            if ($pathItem->security !== null) {
                array_push($security, ...$pathItem->security);
            }
            if ($pathItem->responses !== null) {
                array_push($responses, ...$pathItem->responses);
            }
        }

        return [
            'tags' => $tags !== [] ? array_values(array_unique($tags)) : null,
            'security' => $security !== [] ? $security : null,
            'responses' => $responses !== [] ? $responses : null,
        ];
    }

    protected function resolvePathItemPaths(Specification $specification, PathItemHierarchy $hierarchy): void
    {
        $pathsByPathItem = $this->collectOperationPaths($specification, $hierarchy);

        foreach ($specification->pathItems as $pathItem) {
            $this->mergeAncestorParameters($pathItem, $hierarchy);

            if (!$this->hasSpecProperties($pathItem)) {
                continue;
            }

            $paths = $pathsByPathItem[spl_object_id($pathItem)] ?? [];

            if ($paths === []) {
                continue;
            }

            foreach ($paths as $path) {
                if ($pathItem->path === null) {
                    $pathItem->path = $path;
                } elseif ($pathItem->path !== $path) {
                    $clone = clone $pathItem;
                    $clone->path = $path;
                    $specification->pathItems[] = $clone;
                }
            }
        }
    }

    /**
     * The operation paths each PathItem governs, collected in one pass over the operations.
     *
     * @return array<int, list<string>> keyed by `spl_object_id()` of the governing PathItem
     */
    protected function collectOperationPaths(Specification $specification, PathItemHierarchy $hierarchy): array
    {
        $paths = [];

        foreach ($specification->operations as $operation) {
            if ($operation->path === null) {
                continue;
            }

            $chain = $hierarchy->forOperation($operation);
            if ($chain === []) {
                continue;
            }

            $governing = spl_object_id($chain[count($chain) - 1]);
            if (!in_array($operation->path, $paths[$governing] ?? [], true)) {
                $paths[$governing][] = $operation->path;
            }
        }

        return $paths;
    }

    protected function mergeAncestorParameters(OA\PathItem $pathItem, PathItemHierarchy $hierarchy): void
    {
        $reflector = $pathItem->getClassReflector();
        if (!$reflector instanceof \ReflectionClass) {
            return;
        }

        // the item's own parameters are what the ancestors merge into, so its own entry drops out
        $ancestors = array_slice($hierarchy->chainFor($reflector), 0, -1);

        foreach (array_reverse($ancestors) as $ancestorPathItem) {
            if ($ancestorPathItem->parameters === null) {
                continue;
            }

            $existingKeys = [];
            foreach ($pathItem->parameters ?? [] as $param) {
                $existingKeys[$param->name . ':' . ($param->in ?? '')] = true;
            }

            foreach ($ancestorPathItem->parameters as $param) {
                if (!isset($existingKeys[$param->name . ':' . ($param->in ?? '')])) {
                    $pathItem->parameters ??= [];
                    $pathItem->parameters[] = $param;
                }
            }
        }
    }

    protected function hasSpecProperties(OA\PathItem $pathItem): bool
    {
        return $pathItem->parameters !== null
            || $pathItem->summary !== null
            || $pathItem->description !== null
            || $pathItem->servers !== null;
    }
}
