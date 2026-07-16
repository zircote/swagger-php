<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Re-keys MediaType encoding lists by property name.
 *
 * The assembler collects Encoding objects as a flat list via contains().
 * The compiler expects them as an associative array keyed by the property name
 * the encoding applies to.
 *
 * @implements PipeInterface<Specification>
 */
class MediaType implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    public function __invoke(mixed $payload): null
    {
        $this->processMediaTypes($payload);

        return null;
    }

    protected function processMediaTypes(Specification $specification): void
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
