<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Spec as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Dto extends OA\Schema
{
    /**
     * @param class-string $of
     */
    public function __construct(string $of)
    {
        $class = new \ReflectionClass($of);

        parent::__construct(
            schema: $class->getShortName(),
            required: array_map(
                static fn (\ReflectionProperty $property): string => $property->getName(),
                $class->getProperties(\ReflectionProperty::IS_PUBLIC),
            ),
        );
    }
}
