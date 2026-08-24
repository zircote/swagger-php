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
 * Promotes property encodings and re-keys MediaType encoding lists by property name.
 *
 * Promotes `OA\Encoding` definitions from `OA\Property\Encoded` properties to their
 * parent MediaType, then re-keys the encoding list as an associative array keyed by
 * property name (the format the compiler expects).
 *
 * @implements PipeInterface<Specification>
 */
class MediaTypes implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $index = $payload->buildComponentIndex();
        $this->mergePropertyEncodings($payload, $index);
        $this->rekeyMediaTypeEncodings($payload);

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

            if ($mediaType->schema->ref !== null) {
                $refSchema = $index->findSchema($mediaType->schema->ref);
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

    protected function rekeyMediaTypeEncodings(Specification $specification): void
    {
        foreach ($specification->operations as $operation) {
            if ($operation->requestBody instanceof OA\RequestBody) {
                $this->rekeyEncodings($operation->requestBody->content);
            }

            if ($operation->responses) {
                foreach ($operation->responses as $response) {
                    $this->rekeyEncodings($response->content);
                }
            }

            if ($operation->parameters) {
                foreach ($operation->parameters as $parameter) {
                    $this->rekeyEncodings($parameter->content);
                }
            }
        }

        foreach ($specification->requestBodies as $body) {
            $this->rekeyEncodings($body->content);
        }

        foreach ($specification->responses as $response) {
            $this->rekeyEncodings($response->content);
        }

        foreach ($specification->parameters as $parameter) {
            $this->rekeyEncodings($parameter->content);
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     */
    protected function rekeyEncodings(?array $mediaTypes): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if (!$mediaType->encoding) {
                continue;
            }

            $keyed = [];
            foreach ($mediaType->encoding as $encoding) {
                if ($encoding instanceof OA\Encoding && $encoding->encoding !== null) {
                    $keyed[$encoding->encoding] = $encoding;
                }
            }

            $mediaType->encoding = $keyed !== [] ? $keyed : null;
        }
    }
}
