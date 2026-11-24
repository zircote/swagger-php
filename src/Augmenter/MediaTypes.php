<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Utils\PipeInterface;

/**
 * Promotes property encodings to their media type.
 *
 * Adds the `OA\Encoding` of each `OA\Property\Encoded` property to the media type whose schema
 * holds the property, named after the property where it names none itself.
 *
 * @implements PipeInterface<Specification>
 */
class MediaTypes implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $this->mergePropertyEncodings($payload, $payload->buildComponentIndex());

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    protected function mergePropertyEncodings(Specification $specification, ComponentIndex $index): void
    {
        $specification->getWalker()->visit(OA\MediaType::class, function (OA\MediaType $mediaType) use ($index): void {
            if (!$mediaType->schema instanceof OA\Schema) {
                return;
            }

            $ref = $mediaType->schema->getRefValue();
            if (is_string($ref)) {
                $refSchema = $index->findSchema($ref);
                if ($refSchema instanceof OA\Schema && $refSchema->properties !== null) {
                    $this->mergeEncoded($mediaType, $refSchema->properties);
                }
            } elseif ($mediaType->schema->properties !== null) {
                $this->mergeEncoded($mediaType, $mediaType->schema->properties);
            }
        });
    }

    /**
     * @param list<OA\Property> $properties
     */
    protected function mergeEncoded(OA\MediaType $mediaType, array $properties): void
    {
        foreach ($properties as $property) {
            if ($property instanceof OA\Property\Encoded) {
                $encoding = $property->encoding;
                $encoding->encoding ??= $property->property;
                $mediaType->encoding ??= [];
                $mediaType->encoding[$encoding->encoding] = $encoding;
            }
        }
    }
}
