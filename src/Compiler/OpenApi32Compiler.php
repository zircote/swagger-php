<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;

/**
 * Compiles a Specification into an OpenAPI 3.2.x document array.
 *
 * 3.2 is a superset of 3.1.
 */
class OpenApi32Compiler extends OpenApi31Compiler
{
    protected const VERSIONS = ['3.2.0'];

    public function getVersion(): string
    {
        return '3.2.0';
    }

    public function validate(Specification $specification): array
    {
        parent::validate($specification);

        $tagNames = array_map(fn (OA\Tag $tag): ?string => $tag->name, $specification->tags);

        foreach ($specification->tags as $tag) {
            if ($tag->parent !== null && !in_array($tag->parent, $tagNames, true)) {
                $this->logger->warning('Tag "' . $tag->name . '" references non-existent parent "' . $tag->parent . '"');
                $tag->parent = null;
            }
        }

        $this->validateExamples($specification);

        return $this->logger->entries();
    }

    #[\Override]
    public function compile(Specification $specification): array
    {
        $result = parent::compile($specification);

        $self = $specification->openapi->self ?? null;
        if ($self !== null) {
            // keep $self directly below openapi, where the spec lists it
            return ['openapi' => $result['openapi'], '$self' => $self] + $result;
        }

        return $result;
    }

    #[\Override]
    protected function compileTag(OA\Tag $tag): array
    {
        return $this->filter([
            'name' => $tag->name,
            'summary' => $tag->summary,
            'description' => $tag->description,
            'externalDocs' => $tag->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($tag->externalDocs) : null,
            'parent' => $tag->parent,
            'kind' => $tag->kind,
        ], $tag);
    }

    #[\Override]
    protected function compileServer(OA\Server $server): array
    {
        return $this->append(parent::compileServer($server), ['name' => $server->name]);
    }

    #[\Override]
    protected function compileResponse(OA\Response $response): array
    {
        $result = parent::compileResponse($response);
        if (isset($result['$ref'])) {
            return $result;
        }

        return $this->append($result, ['summary' => $response->summary]);
    }

    #[\Override]
    protected function compileExample(OA\Example $example): array
    {
        $result = $this->append(parent::compileExample($example), ['serializedValue' => $example->serializedValue]);

        if ($example->dataValue !== Undefined::UNDEFINED) {
            $result['dataValue'] = $example->dataValue;
        }

        return $result;
    }

    #[\Override]
    protected function compileSecurityScheme(OA\Security\Scheme $scheme): array
    {
        return $this->append(parent::compileSecurityScheme($scheme), [
            'deprecated' => $scheme->deprecated,
            'oauth2MetadataUrl' => $scheme->type === OA\SchemeType::OAuth2->value ? $scheme->oauth2MetadataUrl : null,
        ]);
    }

    /**
     * Add 3.2-only fields to an already compiled result, dropping the ones not set.
     *
     * @param array<string,mixed> $result
     * @param array<string,mixed> $fields
     */
    protected function append(array $result, array $fields): array
    {
        foreach ($fields as $key => $value) {
            if ($value !== null) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * `value` is the pre-3.2 way of embedding an example, and is mutually exclusive with both
     * of the fields that replace it. `serializedValue` and `externalValue` overlap as well —
     * both describe the example after serialization.
     */
    protected function validateExamples(Specification $specification): void
    {
        $specification->getWalker()->visit(OA\Example::class, function (OA\Example $example): void {
            $hasDataValue = $example->dataValue !== Undefined::UNDEFINED;

            if ($example->value !== Undefined::UNDEFINED && ($hasDataValue || $example->serializedValue !== null)) {
                $this->logger->warning('Example value is mutually exclusive with dataValue and serializedValue in ' . $example->getSourceLocation());
            }

            if ($example->serializedValue !== null && $example->externalValue !== null) {
                $this->logger->warning('Example serializedValue and externalValue are mutually exclusive in ' . $example->getSourceLocation());
            }
        });
    }
}
