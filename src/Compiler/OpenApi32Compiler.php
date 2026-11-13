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
    protected const VERSION_PREFIX = '3.2.';

    protected const OPERATION_METHODS = [...parent::OPERATION_METHODS, 'query'];

    protected const OPERATION_REQUEST_BODY_METHODS = [...parent::OPERATION_REQUEST_BODY_METHODS, 'query'];

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
                $this->logger->error('Tag "' . $tag->name . '" names parent "' . $tag->parent . '", which does not exist in ' . $tag->getSourceLocation());
                $tag->parent = null;
            }
        }

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
    protected function validateSpecification(Specification $specification): void
    {
        parent::validateSpecification($specification);

        $this->validateExamples($specification);
    }

    /**
     * `value` is the pre-3.2 way of embedding an example, and is mutually exclusive with both
     * of the fields that replace it. `serializedValue` and `externalValue` overlap as well:
     * both describe the example after serialization.
     */
    protected function validateExamples(Specification $specification): void
    {
        $this->visit($specification, OA\Example::class, function (OA\Example $example): void {
            $subject = $example->example === null ? 'Example' : 'Example "' . $example->example . '"';

            if ($example->value !== Undefined::UNDEFINED && ($example->dataValue !== Undefined::UNDEFINED || $example->serializedValue !== null)) {
                $this->logger->error("{$subject} value is mutually exclusive with dataValue and serializedValue in " . $example->getSourceLocation());
            }

            if ($example->serializedValue !== null && $example->externalValue !== null) {
                $this->logger->error("{$subject} serializedValue and externalValue are mutually exclusive in " . $example->getSourceLocation());
            }
        });
    }

    /**
     * A response has a summary of its own from 3.2 on. Its headers and links still do not.
     */
    #[\Override]
    protected function summariesWithoutRef(array $attributes): array
    {
        $subjects = [];
        foreach ($attributes as $attribute) {
            if ($attribute instanceof OA\Response && $attribute->getRefValue() === null) {
                $checked = [...$attribute->headers ?? [], ...$attribute->links ?? []];
            } else {
                $checked = [$attribute];
            }

            $subjects = [...$subjects, ...parent::summariesWithoutRef($checked)];
        }

        return $subjects;
    }

    #[\Override]
    protected function validateVersionedComponents(Specification $specification): void
    {
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileVersionedComponents(Specification $specification): array
    {
        return [
            'mediaTypes' => $this->compileComponentMap($specification->mediaTypes, $this->compileMediaType(...)),
        ];
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileServer(OA\Server $server): array
    {
        return $this->append(parent::compileServer($server), ['name' => $server->name]);
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileResponse(OA\Response $response): array
    {
        $result = parent::compileResponse($response);
        if ($response->ref !== null) {
            return $result;
        }

        return $this->filter(['summary' => $response->summary]) + $result;
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileExample(OA\Example $example): array
    {
        $result = parent::compileExample($example);
        if ($example->ref !== null) {
            return $result;
        }

        $result = $this->append($result, ['serializedValue' => $example->serializedValue]);

        return $this->withDefined($result, ['dataValue' => $example->dataValue]);
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileDiscriminator(OA\Discriminator $discriminator): array
    {
        return $this->append(parent::compileDiscriminator($discriminator), ['defaultMapping' => $discriminator->defaultMapping]);
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileSecurityScheme(OA\Security\Scheme $scheme): array
    {
        $result = parent::compileSecurityScheme($scheme);
        if ($scheme->ref !== null) {
            return $result;
        }

        return $this->append($result, [
            'deprecated' => $scheme->deprecated,
            'oauth2MetadataUrl' => $scheme->type === OA\SchemeType::OAuth2->value ? $scheme->oauth2MetadataUrl : null,
        ]);
    }

    /**
     * Adds the 3.2 fields that are set to an already compiled result, ahead of its extensions.
     *
     * @param  array<string,mixed> $result
     * @param  array<string,mixed> $fields
     * @return array<string,mixed>
     */
    protected function append(array $result, array $fields): array
    {
        $extensions = array_filter($result, fn (string|int $key): bool => str_starts_with((string) $key, 'x-'), ARRAY_FILTER_USE_KEY);

        return [...array_diff_key($result, $extensions), ...$this->filter($fields), ...$extensions];
    }
}
