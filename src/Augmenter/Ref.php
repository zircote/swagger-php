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
class Ref implements PipeInterface, LoggerAwareInterface
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

        $unresolved = [];
        $payload->getWalker()->eachRef(function (OA\AbstractAttribute $attribute) use ($refMap, &$unresolved): void {
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

        $this->resolveDiscriminatorMappings($payload, $refMap);

        return null;
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
            $fqcn = $this->getClassName($schema);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/schemas/' . $name;
            }
        }

        foreach ($specification->responses as $response) {
            $name = $response->response;
            $fqcn = $this->getClassName($response);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/responses/' . $name;
            }
        }

        foreach ($specification->requestBodies as $body) {
            $name = $body->request;
            $fqcn = $this->getClassName($body);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/requestBodies/' . $name;
            }
        }

        foreach ($specification->headers as $header) {
            $name = $header->header;
            $fqcn = $this->getClassName($header);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/headers/' . $name;
            }
        }

        foreach ($specification->parameters as $parameter) {
            $name = $parameter->parameter ?? $parameter->name;
            $fqcn = $this->getClassName($parameter);
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

    protected function getClassName(OA\AbstractAttribute $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if ($reflector instanceof \ReflectionClass) {
            return $reflector->getName();
        }

        return null;
    }
}
