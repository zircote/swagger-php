<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\PipeInterface;

/**
 * Resolves shortcut attributes.
 *
 * Handles:
 * * `OA\MediaType\Json`
 * * `OA\MediaType\Xml`
 * * `OA\Schema\Items`
 *
 * @implements PipeInterface<Specification>
 */
class Shortcuts implements PipeInterface
{
    private const MEDIA_TYPE_SCHEMA_PROPERTIES = ['ref', 'type', 'items', 'properties', 'required'];

    private const ITEMS_KEEP_PROPERTIES = ['schema', 'title', 'description', 'deprecated', 'readOnly', 'writeOnly', 'xml', 'externalDocs', 'x', 'attachables'];

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        $this->processMediaTypes($payload);
        $this->processSchemaItems($payload);

        return null;
    }

    protected function processMediaTypes(Specification $specification): void
    {
        $specification->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'content') && $attribute->content !== null && is_array($attribute->content)) {
                foreach ($attribute->content as $mediaType) {
                    if ($mediaType instanceof OA\MediaType\Json || $mediaType instanceof OA\MediaType\Xml) {
                        $this->processMediaType($mediaType);
                    }
                }
            }
        });
    }

    protected function processMediaType(OA\MediaType\Json|OA\MediaType\Xml $mediaType): void
    {
        if (!$mediaType->schema instanceof OA\Schema) {
            $args = [];
            foreach (self::MEDIA_TYPE_SCHEMA_PROPERTIES as $prop) {
                if ($mediaType->{$prop} !== null) {
                    $args[$prop] = $mediaType->{$prop};
                    $mediaType->{$prop} = null;
                }
            }
            $mediaType->schema = new OA\Schema(...$args);
        } else {
            foreach (self::MEDIA_TYPE_SCHEMA_PROPERTIES as $prop) {
                if ($mediaType->{$prop} !== null && $mediaType->schema->{$prop} === null) {
                    $mediaType->schema->{$prop} = $mediaType->{$prop};
                    $mediaType->{$prop} = null;
                }
            }
        }
    }

    protected function processSchemaItems(Specification $specification): void
    {
        $specification->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'schema') && $attribute->schema instanceof OA\Schema\Items) {
                $this->processSchemaItem($attribute->schema);
            }
        });
    }

    protected function processSchemaItem(OA\Schema\Items $items): void
    {
        if ($items->items instanceof OA\Schema\Items) {
            $this->processSchemaItem($items->items);
        }

        $itemKeep = [...OA\Schema::ARRAY_PROPERTIES, ...self::ITEMS_KEEP_PROPERTIES];

        $innerArgs = [];
        foreach ((new \ReflectionClass(OA\Schema::class))->getConstructor()->getParameters() as $param) {
            $prop = $param->getName();
            if (in_array($prop, $itemKeep, true)) {
                continue;
            }
            $value = $items->{$prop};
            if ($value !== null && $value !== Undefined::UNDEFINED) {
                $innerArgs[$prop] = $value;
                $items->{$prop} = null;
            }
        }

        $items->type = 'array';
        if ($innerArgs || $items->items === null) {
            $items->items = new OA\Schema(...$innerArgs);
        }
    }
}
