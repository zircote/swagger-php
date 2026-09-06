<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Contracts\AttributeTranslatorInterface;
use OpenApi\Spec as OA;

final class RouteTranslator implements AttributeTranslatorInterface
{
    public function reset(): void
    {
    }

    public function getAttributes(
        \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector,
    ): array {
        return $reflector->getAttributes(Route::class);
    }

    public function translate(
        array $attributes,
        array $created,
        \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector,
    ): array {
        foreach ($created as $route) {
            if ($route instanceof Route) {
                $attributes[] = new OA\Operation(
                    path: $route->path,
                    method: $route->method,
                    operationId: $reflector->getName(),
                );
            }
        }

        return $attributes;
    }
}
