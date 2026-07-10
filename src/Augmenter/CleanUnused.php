<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Removes unreferenced components from the specification.
 *
 * Iterates multiple times to catch nested dependencies (a schema only
 * referenced by another unused schema should also be removed).
 *
 * @implements PipeInterface<Specification>
 */
class CleanUnused implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const MAX_ITERATIONS = 10;

    protected bool $enabled;

    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Reduce;
    }

    public function __invoke(mixed $payload): mixed
    {
        if (!$this->enabled) {
            return null;
        }

        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            if (!$this->cleanup($payload)) {
                return null;
            }
        }

        $this->logger?->warning('CleanUnused: maximum iterations ({max}) reached, some unused components may remain', [
            'max' => self::MAX_ITERATIONS,
        ]);

        return null;
    }

    protected function cleanup(Specification $specification): bool
    {
        $usedRefs = [];
        $specification->eachRef(static function (OA\AbstractAttribute $attribute) use (&$usedRefs): void {
            $usedRefs[$attribute->ref] = true;
        });

        $results = [
            $this->removeUnusedSchemas($specification, $usedRefs),
            $this->removeUnusedResponses($specification, $usedRefs),
            $this->removeUnusedParameters($specification, $usedRefs),
            $this->removeUnusedRequestBodies($specification, $usedRefs),
            $this->removeUnusedHeaders($specification, $usedRefs),
            $this->removeUnusedSecuritySchemes($specification, $usedRefs),
            $this->removeUnusedLinks($specification, $usedRefs),
            $this->removeUnusedExamples($specification, $usedRefs),
        ];

        return in_array(true, $results, true);
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedSchemas(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->schemas as $index => $schema) {
            $name = $schema->schema ?? $schema->title;
            if ($name !== null && !isset($usedRefs['#/components/schemas/' . $name])) {
                unset($specification->schemas[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->schemas = array_values($specification->schemas);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedResponses(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->responses as $index => $response) {
            $name = $response->response;
            if ($name !== null && !isset($usedRefs['#/components/responses/' . $name])) {
                unset($specification->responses[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->responses = array_values($specification->responses);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedParameters(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->parameters as $index => $parameter) {
            $name = $parameter->parameter ?? $parameter->name;
            if ($name !== null && !isset($usedRefs['#/components/parameters/' . $name])) {
                unset($specification->parameters[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->parameters = array_values($specification->parameters);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedRequestBodies(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->requestBodies as $index => $body) {
            $name = $body->request;
            if ($name !== null && !isset($usedRefs['#/components/requestBodies/' . $name])) {
                unset($specification->requestBodies[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->requestBodies = array_values($specification->requestBodies);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedHeaders(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->headers as $index => $header) {
            $name = $header->header;
            if ($name !== null && !isset($usedRefs['#/components/headers/' . $name])) {
                unset($specification->headers[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->headers = array_values($specification->headers);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedSecuritySchemes(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->securitySchemes as $index => $scheme) {
            $name = $scheme->securityScheme;
            if ($name !== null && !isset($usedRefs['#/components/securitySchemes/' . $name])) {
                unset($specification->securitySchemes[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->securitySchemes = array_values($specification->securitySchemes);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedLinks(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->links as $index => $link) {
            $name = $link->link;
            if ($name !== null && !isset($usedRefs['#/components/links/' . $name])) {
                unset($specification->links[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->links = array_values($specification->links);
        }

        return $removed;
    }

    /**
     * @param array<string, true> $usedRefs
     */
    protected function removeUnusedExamples(Specification $specification, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->examples as $index => $example) {
            $name = $example->example;
            if ($name !== null && !isset($usedRefs['#/components/examples/' . $name])) {
                unset($specification->examples[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->examples = array_values($specification->examples);
        }

        return $removed;
    }
}
