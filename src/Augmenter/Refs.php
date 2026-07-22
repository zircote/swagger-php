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
 * Resolves FQCN-based $ref values to JSON Reference paths.
 *
 * Builds a map of class names to their component paths and rewrites
 * any $ref that looks like a FQCN into the proper #/components/... path.
 *
 * @implements PipeInterface<Specification>
 */
class Refs implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        $refMap = $this->buildRefMap($payload);

        if ($refMap === []) {
            return null;
        }

        $this->resolveFQCNRefs($payload, $refMap);
        $this->resolveDiscriminatorMappings($payload, $refMap);
        $this->resolveAllOfPropertyRefs($payload);

        return null;
    }

    protected function resolveFQCNRefs(Specification $specification, array &$refMap): array
    {
        $unresolved = [];

        $specification->getWalker()->eachRef(function (OA\AbstractAttribute $attribute) use ($refMap, &$unresolved): void {
            if (str_starts_with($attribute->ref, '#/')) {
                return;
            }
            if (isset($refMap[$attribute->ref])) {
                $attribute->ref = $refMap[$attribute->ref];
            } else {
                $unresolved[$attribute->ref] = true;
            }
        });

        foreach (array_keys($unresolved) as $ref) {
            $this->logger?->warning('Ref: unresolved reference "{ref}" — no matching component found', [
                'ref' => $ref,
            ]);
        }

        return $refMap;
    }

    /**
     * Build a map of FQCN → #/components/{type}/{name}.
     *
     * @return array<string, string>
     */
    protected function buildRefMap(Specification $specification): array
    {
        $map = [];

        foreach ($specification->schemas as $schema) {
            $name = $schema->schema ?? $schema->title;
            $fqcn = $schema->getClassName();
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/schemas/' . $name;
            }
        }

        foreach ($specification->responses as $response) {
            $name = $response->response;
            $fqcn = $response->getClassName();
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/responses/' . $name;
            }
        }

        foreach ($specification->requestBodies as $body) {
            $name = $body->request;
            $fqcn = $body->getClassName();
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/requestBodies/' . $name;
            }
        }

        foreach ($specification->headers as $header) {
            $name = $header->header;
            $fqcn = $header->getClassName();
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/headers/' . $name;
            }
        }

        foreach ($specification->parameters as $parameter) {
            $name = $parameter->parameter ?? $parameter->name;
            $fqcn = $parameter->getClassName();
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/parameters/' . $name;
            }
        }

        return $map;
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveDiscriminatorMappings(Specification $specification, array $refMap): void
    {
        foreach ($specification->schemas as $schema) {
            if (!$schema->discriminator instanceof OA\Discriminator || $schema->discriminator->mapping === null) {
                continue;
            }

            foreach ($schema->discriminator->mapping as $value => $type) {
                if (str_starts_with($type, '#/')) {
                    continue;
                }
                if (isset($refMap[$type])) {
                    $schema->discriminator->mapping[$value] = $refMap[$type];
                }
            }
        }
    }

    /**
     * Adjust refs pointing to schema properties that got merged into `allOf` during inheritance processing.
     */
    protected function resolveAllOfPropertyRefs(Specification $specification): void
    {
        $candidates = [];

        $specification->getWalker()->visit(OA\Schema::class, function (OA\Schema $schema) use (&$candidates): void {
            if ($schema->allOf !== null && $schema->properties === null) {
                foreach ($schema->allOf as $index => $allOf) {
                    if ($allOf instanceof OA\Schema && $allOf->properties !== null) {
                        $name = $schema->schema ?? $schema->title;
                        $candidates[$name] = $index;
                    }
                }
            }
        });

        $specification->getWalker()->eachRef(function (OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme $attribute) use (&$candidates): void {
            preg_match('/#\/components\/schemas\/([^\/]+)\/(properties\/.+)/', (string) $attribute->ref, $matches);

            if (count($matches) !== 3) {
                return;
            }

            $name = $matches[1];
            $path = $matches[2];
            if (array_key_exists($name, $candidates)) {
                $index = $candidates[$name];
                $attribute->ref = "#/components/schemas/{$name}/allOf/{$index}/{$path}";
            }
        });
    }
}
