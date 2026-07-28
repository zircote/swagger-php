<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Resolves shortcut attributes.
 *
 * Handles:
 * * `OA\MediaType\Json`
 * * `OA\MediaType\Xml`
 *
 * @implements PipeInterface<Specification>
 */
class Shortcuts implements PipeInterface
{
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
            $mediaType->schema = new OA\Schema(
                ref: $mediaType->ref,
                type: $mediaType->type,
                items: $mediaType->items,
                properties: $mediaType->properties,
                required: $mediaType->required,
            );
        }
    }

    protected function processSchemaItems(Specification $specification): void
    {
        $specification->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'schema') && $attribute->schema instanceof OA\Schema\Items) {
                $this->processSchemaItem($attribute);
            }
        });
    }

    protected function processSchemaItem(AttributeInterface $parent): void
    {
        if (!property_exists($parent, 'schema') || !$parent->schema instanceof OA\Schema\Items) {
            return;
        }

        $arrayConstraints = [
            'minItems',
            'maxItems',
            'uniqueItems',
            'prefixItems',
            'contains',
            'minContains',
            'maxContains',
            'unevaluatedItems',
        ];

        $inner = $parent->schema;
        $outer = new OA\Schema(
            type: 'array',
            items: $inner,
        );
        foreach ($arrayConstraints as $arrayConstraint) {
            $outer->{$arrayConstraint} = $inner->{$arrayConstraint};
        }
        foreach ($arrayConstraints as $arrayConstraint) {
            $inner->{$arrayConstraint} = null;
        }

        $parent->schema = $outer;
    }
}
