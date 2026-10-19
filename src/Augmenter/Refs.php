<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\JsonPointer;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Resolves FQCN-based $ref values to JSON Reference paths.
 *
 * Builds a map of class names to their component paths and rewrites
 * any $ref that looks like a FQCN into the proper #/components/... path.
 * A ref that is neither a known class nor a pointer to an existing component is reported.
 *
 * @implements PipeInterface<Specification>
 */
class Refs implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->schemas as $schema) {
            if ($schema->getClassReflector() === null) {
                continue;
            }
            $this->mergeAllOf($schema);
        }

        $index = $payload->buildComponentIndex();
        $refMap = $index->buildRefMap();

        $this->resolveRefRefs($payload);
        $this->resolveFQCNRefs($payload, $refMap, $index);

        // deduplicate once every ref carries its final value: a FQCN and the component
        // pointer added by Augmenter\Inheritance name the same schema but differ as strings
        foreach ($payload->schemas as $schema) {
            if ($schema->getClassReflector() === null) {
                continue;
            }
            $this->dedupAllOfRefs($schema);
        }

        if ($refMap === []) {
            return null;
        }

        $this->resolveDiscriminatorMappings($payload, $refMap);
        $this->resolveAllOfPropertyRefs($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function resolveRefRefs(Specification $specification): void
    {
        $specification->getWalker()->eachRef(function (AttributeInterface $attribute): void {
            if (property_exists($attribute, 'ref') && $attribute->ref !== null && $attribute->ref instanceof OA\Schema\Ref) {
                $attribute->ref = $attribute->ref->ref;
            }
        });
    }

    /**
     * @param array<string,string> $refMap
     */
    protected function resolveFQCNRefs(Specification $specification, array $refMap, ComponentIndex $index): void
    {
        $unresolved = [];
        $dangling = [];

        $specification->getWalker()->eachRef(function (AttributeInterface $attribute) use ($refMap, $index, &$unresolved, &$dangling): void {
            if (!property_exists($attribute, 'ref') || !is_string($attribute->ref)) {
                return;
            }

            if (str_starts_with($attribute->ref, '#/')) {
                if ($this->isDangling($attribute->ref, $index)) {
                    $dangling[$attribute->ref] = true;
                }
            } elseif (isset($refMap[$attribute->ref])) {
                $attribute->ref = $refMap[$attribute->ref];
            } else {
                $unresolved[$attribute->ref] = true;
            }
        });

        foreach (array_keys($unresolved) as $ref) {
            $pointer = $this->pointerFor((string) $ref, $index);
            $this->logger?->warning($pointer !== null
                ? "Ref: unresolved reference '{$ref}' — a component of that name exists, use '{$pointer}'"
                : "Ref: unresolved reference '{$ref}' — no matching component found");
        }

        foreach (array_keys($dangling) as $ref) {
            $this->logger?->warning("Ref: dangling reference '{$ref}' — no such component");
        }
    }

    /**
     * A pointer into a component bucket whose component does not exist. Pointers anywhere else are not checked,
     * and neither are security schemes: the walker makes a pointer for each scheme a requirement names.
     */
    protected function isDangling(string $ref, ComponentIndex $index): bool
    {
        $segments = explode('/', $ref);
        if (count($segments) < 4 || $segments[1] !== 'components' || $segments[2] === 'securitySchemes'
            || !in_array($segments[2], ComponentName::BUCKETS, true)) {
            return false;
        }

        return !$index->find(JsonPointer::ref('components', $segments[2], JsonPointer::decode($segments[3]))) instanceof AttributeInterface;
    }

    /**
     * The pointer to the component a bare name matches, if there is one.
     */
    protected function pointerFor(string $name, ComponentIndex $index): ?string
    {
        foreach (ComponentName::BUCKETS as $bucket) {
            $pointer = JsonPointer::ref('components', $bucket, $name);
            if ($index->find($pointer) instanceof AttributeInterface) {
                return $pointer;
            }
        }

        return null;
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
                        $name = ComponentName::of($schema);
                        if ($name !== null) {
                            $candidates[$name] = $index;
                        }
                    }
                }
            }
        });

        $specification->getWalker()->eachRef(function (AttributeInterface $attribute) use (&$candidates): void {
            if (!property_exists($attribute, 'ref') || $attribute->ref === null) {
                return;
            }

            preg_match('/#\/components\/schemas\/([^\/]+)\/(properties\/.+)/', (string) $attribute->ref, $matches);

            if (count($matches) !== 3) {
                return;
            }

            // the captured name is escaped, `$candidates` is keyed by the raw one
            $name = JsonPointer::decode($matches[1]);
            $path = $matches[2];
            if (array_key_exists($name, $candidates)) {
                $index = $candidates[$name];
                // `$path` is copied through as captured — it is already-escaped tokens plus separators
                $attribute->ref = JsonPointer::ref('components', 'schemas', $name, 'allOf', (string) $index) . '/' . $path;
            }
        });
    }

    /**
     * When allOf refs were added but the schema also has own properties, wrap them
     * in a dedicated allOf entry so the final output is a pure allOf composition.
     */
    protected function mergeAllOf(OA\Schema $schema): void
    {
        if ($schema->allOf === null || $schema->properties === null) {
            return;
        }

        $schema->allOf[] = new OA\Schema(type: 'object', properties: $schema->properties);
        $schema->properties = null;
    }

    protected function dedupAllOfRefs(OA\Schema $schema): void
    {
        if ($schema->allOf === null || $schema->allOf === []) {
            return;
        }

        $unique = [];
        foreach ($schema->allOf as $ii => $allOf) {
            // resolveRefRefs() has collapsed every Schema\Ref into its string by now
            $ref = $allOf->ref instanceof OA\Schema\Ref ? $allOf->ref->ref : $allOf->ref;
            if (is_string($ref)) {
                if (isset($unique[$ref])) {
                    continue;
                }
                $unique[$ref] = $allOf;
            } else {
                $unique[$ii] = $allOf;
            }
        }
        $schema->allOf = array_values($unique);
    }
}
