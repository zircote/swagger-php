<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Resolves PathItem prefixes, clones metadata to operations, and sets path-level output.
 *
 * Walks the class hierarchy to compose path prefixes from ancestor PathItems,
 * prepends them to operation paths, clones tags/security/responses to operations
 * that don't declare their own, and marks PathItems that have spec-level output
 * (parameters, summary, description, servers) with their resolved path.
 *
 * @implements PipeInterface<Specification>
 */
class PathItemResolve implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        $classToPathItem = $this->indexPathItems($payload);

        if ($classToPathItem === []) {
            return null;
        }

        $prefixCache = [];

        foreach ($payload->operations as $operation) {
            $className = $this->getDeclaringClass($operation);
            if ($className === null) {
                continue;
            }

            $pathItem = $this->findGoverningPathItem($className, $classToPathItem);
            if (!$pathItem instanceof OA\PathItem) {
                continue;
            }

            $prefix = $this->resolvePrefix($pathItem, $classToPathItem, $prefixCache);
            if ($prefix !== '' && $operation->path !== null) {
                $operationPath = ltrim($operation->path, '/');
                $operation->path = $operationPath !== '' ? $prefix . '/' . $operationPath : $prefix;
            }

            $this->cloneMetadata($pathItem, $operation, $classToPathItem);
        }

        $this->resolvePathItemPaths($payload, $classToPathItem, $prefixCache);

        return null;
    }

    /**
     * @return array<class-string, OA\PathItem>
     */
    protected function indexPathItems(Specification $specification): array
    {
        $map = [];

        foreach ($specification->pathItems as $pathItem) {
            $reflector = $pathItem->getReflector();
            if ($reflector instanceof \ReflectionClass) {
                $map[$reflector->getName()] = $pathItem;
            }
        }

        return $map;
    }

    /**
     * @param array<class-string, OA\PathItem> $classToPathItem
     * @param array<class-string, string>      $cache
     */
    protected function resolvePrefix(OA\PathItem $pathItem, array $classToPathItem, array &$cache): string
    {
        $reflector = $pathItem->getReflector();
        if (!$reflector instanceof \ReflectionClass) {
            return $pathItem->prefix ?? '';
        }

        $className = $reflector->getName();
        if (isset($cache[$className])) {
            return $cache[$className];
        }

        $parts = [];
        $current = $reflector;
        while ($current !== false) {
            if (isset($classToPathItem[$current->getName()])) {
                $pi = $classToPathItem[$current->getName()];
                if ($pi->prefix !== null) {
                    $parts[] = trim($pi->prefix, '/');
                }
            }
            $current = $current->getParentClass();
        }

        $parts = array_filter(array_reverse($parts), static fn (string $p): bool => $p !== '');
        $prefix = $parts !== [] ? '/' . implode('/', $parts) : '';
        $cache[$className] = $prefix;

        return $prefix;
    }

    /**
     * @param array<class-string, OA\PathItem> $classToPathItem
     */
    protected function findGoverningPathItem(string $className, array $classToPathItem): ?OA\PathItem
    {
        if (isset($classToPathItem[$className])) {
            return $classToPathItem[$className];
        }

        try {
            $rc = new \ReflectionClass($className);
        } catch (\ReflectionException) {
            return null;
        }

        $parent = $rc->getParentClass();
        while ($parent !== false) {
            if (isset($classToPathItem[$parent->getName()])) {
                return $classToPathItem[$parent->getName()];
            }
            $parent = $parent->getParentClass();
        }

        return null;
    }

    /**
     * @param array<class-string, OA\PathItem> $classToPathItem
     */
    protected function cloneMetadata(OA\PathItem $pathItem, OA\Operation $operation, array $classToPathItem): void
    {
        $merged = $this->collectMergedMetadata($pathItem, $classToPathItem);

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
     * Collect merged metadata walking up the class hierarchy.
     * All collections merge additively — tags, security, and responses accumulate from all ancestors.
     *
     * @param  array<class-string, OA\PathItem>                                                                                $classToPathItem
     * @return array{tags: list<string>|null, security: list<OA\Security\Requirement>|null, responses: list<OA\Response>|null}
     */
    protected function collectMergedMetadata(OA\PathItem $pathItem, array $classToPathItem): array
    {
        $reflector = $pathItem->getReflector();
        if (!$reflector instanceof \ReflectionClass) {
            return [
                'tags' => $pathItem->tags,
                'security' => $pathItem->security,
                'responses' => $pathItem->responses,
            ];
        }

        $tags = [];
        $security = [];
        $responses = [];

        $current = $reflector;
        while ($current !== false) {
            $pi = $classToPathItem[$current->getName()] ?? null;
            if ($pi !== null) {
                if ($pi->tags !== null) {
                    array_push($tags, ...$pi->tags);
                }
                if ($pi->security !== null) {
                    array_push($security, ...$pi->security);
                }
                if ($pi->responses !== null) {
                    array_push($responses, ...$pi->responses);
                }
            }
            $current = $current->getParentClass();
        }

        return [
            'tags' => $tags !== [] ? array_values(array_unique($tags)) : null,
            'security' => $security !== [] ? $security : null,
            'responses' => $responses !== [] ? $responses : null,
        ];
    }

    protected function getDeclaringClass(OA\Operation $operation): ?string
    {
        $reflector = $operation->getReflector();

        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->getDeclaringClass()->getName();
        }

        if ($reflector instanceof \ReflectionClass) {
            return $reflector->getName();
        }

        return null;
    }

    /**
     * @param array<class-string, OA\PathItem> $classToPathItem
     * @param array<class-string, string>      $prefixCache
     */
    protected function resolvePathItemPaths(Specification $specification, array $classToPathItem, array $prefixCache): void
    {
        foreach ($specification->pathItems as $pathItem) {
            $this->mergeAncestorParameters($pathItem, $classToPathItem);

            if (!$this->hasSpecProperties($pathItem)) {
                continue;
            }

            $paths = $this->findOperationPaths($pathItem, $specification, $classToPathItem);

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
     * @param array<class-string, OA\PathItem> $classToPathItem
     */
    protected function mergeAncestorParameters(OA\PathItem $pathItem, array $classToPathItem): void
    {
        $reflector = $pathItem->getReflector();
        if (!$reflector instanceof \ReflectionClass) {
            return;
        }

        $parent = $reflector->getParentClass();
        while ($parent !== false) {
            $ancestorPi = $classToPathItem[$parent->getName()] ?? null;
            if ($ancestorPi !== null && $ancestorPi->parameters !== null) {
                $existingKeys = [];
                foreach ($pathItem->parameters ?? [] as $param) {
                    $existingKeys[$param->name . ':' . ($param->in ?? '')] = true;
                }

                foreach ($ancestorPi->parameters as $param) {
                    if (!isset($existingKeys[$param->name . ':' . ($param->in ?? '')])) {
                        $pathItem->parameters ??= [];
                        $pathItem->parameters[] = $param;
                    }
                }
            }
            $parent = $parent->getParentClass();
        }
    }

    protected function hasSpecProperties(OA\PathItem $pathItem): bool
    {
        return $pathItem->parameters !== null
            || $pathItem->summary !== null
            || $pathItem->description !== null
            || $pathItem->servers !== null;
    }

    /**
     * @param  array<class-string, OA\PathItem> $classToPathItem
     * @return list<string>
     */
    protected function findOperationPaths(OA\PathItem $pathItem, Specification $specification, array $classToPathItem): array
    {
        if (!$pathItem->getReflector() instanceof \ReflectionClass) {
            return [];
        }

        $paths = [];

        foreach ($specification->operations as $operation) {
            if ($operation->path === null) {
                continue;
            }

            $opClass = $this->getDeclaringClass($operation);
            if ($opClass === null) {
                continue;
            }

            $governing = $this->findGoverningPathItem($opClass, $classToPathItem);
            if ($governing === $pathItem && !in_array($operation->path, $paths, true)) {
                $paths[] = $operation->path;
            }
        }

        return $paths;
    }
}
