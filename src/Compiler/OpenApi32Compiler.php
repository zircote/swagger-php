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

    protected const UNSUPPORTED_PROPERTIES = [];

    protected const UNSUPPORTED_VALUES = [];

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
    protected function validateSpecification(Specification $specification): void
    {
        parent::validateSpecification($specification);

        $this->validateMetadataUrls($specification);
    }

    /**
     * Only an `oauth2` scheme takes `oauth2MetadataUrl`, so on any other type it is left out.
     */
    protected function validateMetadataUrls(Specification $specification): void
    {
        $this->visit($specification, OA\Security\Scheme::class, function (OA\Security\Scheme $scheme): void {
            if ($scheme->oauth2MetadataUrl !== null && $scheme->type !== OA\SchemeType::OAuth2->value) {
                $this->logger->warning("{$this->subject($scheme)} uses oauth2MetadataUrl, which only an oauth2 scheme takes, omitted"
                    . ($scheme->getReflector() instanceof \Reflector ? ' in ' . $scheme->getSourceLocation() : ''));
            }
        });
    }

    /**
     * `value` is the pre-3.2 way of embedding an example, and is mutually exclusive with both
     * of the fields that replace it. `serializedValue` and `externalValue` overlap as well:
     * both describe the example after serialization.
     */
    #[\Override]
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

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileExample(OA\Example $example): array
    {
        if ($example->ref !== null) {
            return $this->compileReference($example);
        }

        $result = $this->filter([
            'summary' => $example->summary,
            'description' => $example->description,
            'externalValue' => $example->externalValue,
            'serializedValue' => $example->serializedValue,
        ], $example);

        return $this->withDefined($result, ['value' => $example->value, 'dataValue' => $example->dataValue]);
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
}
