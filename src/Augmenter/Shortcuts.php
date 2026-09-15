<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
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
    private const ITEMS_KEEP_PROPERTIES = ['schema', 'title', 'description', 'deprecated', 'readOnly', 'writeOnly', 'xml', 'externalDocs', 'x', 'attachables'];

    public function __invoke(mixed $payload): mixed
    {
        $this->processMediaTypes($payload);
        $this->processSchemaItems($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
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
        // spelled out per property rather than looped over a list of property names: the
        // shortcut properties mirror OA\Schema's one for one, but a dynamic
        // `$x->{$prop} = $y->{$prop}` makes that invisible — phpstan can only see the union
        // of all five types on each side, so every assignment looks like a type error
        if (!$mediaType->schema instanceof OA\Schema) {
            $mediaType->schema = new OA\Schema(
                ref: $mediaType->ref,
                type: $mediaType->type,
                items: $mediaType->items,
                properties: $mediaType->properties,
                required: $mediaType->required,
            );
            $mediaType->ref = null;
            $mediaType->type = null;
            $mediaType->items = null;
            $mediaType->properties = null;
            $mediaType->required = null;

            return;
        }

        // only a shortcut the schema has no value for is moved across; one the schema already
        // sets is left on the media type untouched, as it was before
        $schema = $mediaType->schema;
        if ($mediaType->ref !== null && $schema->ref === null) {
            $schema->ref = $mediaType->ref;
            $mediaType->ref = null;
        }
        if ($mediaType->type !== null && $schema->type === null) {
            $schema->type = $mediaType->type;
            $mediaType->type = null;
        }
        if ($mediaType->items !== null && $schema->items === null) {
            $schema->items = $mediaType->items;
            $mediaType->items = null;
        }
        if ($mediaType->properties !== null && $schema->properties === null) {
            $schema->properties = $mediaType->properties;
            $mediaType->properties = null;
        }
        if ($mediaType->required !== null && $schema->required === null) {
            $schema->required = $mediaType->required;
            $mediaType->required = null;
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
