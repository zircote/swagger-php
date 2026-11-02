<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\RefInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentKey;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\JsonPointer;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Resolves FQCN-based $ref values to JSON Reference paths.
 *
 * A $ref that looks like a FQCN is looked up by its `ComponentKey`, in the bucket the ref points
 * into, and rewritten into the proper #/components/... path. A `Schema\Ref` adds its qualifiers
 * to the key. A ref that is neither a known class nor a pointer to an existing component is
 * reported, and so is one whose key names more than one component.
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

        // keys first: a qualified Schema\Ref loses its qualifiers when collapsed into its string
        $this->resolveFQCNRefs($payload, $index);
        $this->resolveRefRefs($payload);

        // deduplicate once every ref carries its final value: a FQCN and the component
        // pointer added by Augmenter\Inheritance name the same schema but differ as strings
        foreach ($payload->schemas as $schema) {
            if ($schema->getClassReflector() === null) {
                continue;
            }
            $this->dedupAllOfRefs($schema);
        }

        $this->resolveDiscriminatorMappings($payload, $index);
        $this->resolveAllOfPropertyRefs($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function resolveRefRefs(Specification $specification): void
    {
        $specification->getWalker()->eachRef(function (RefInterface $attribute): void {
            $attribute->setRef($attribute->getRefValue());
        });
    }

    protected function resolveFQCNRefs(Specification $specification, ComponentIndex $index): void
    {
        $unresolved = [];
        $dangling = [];
        $ambiguous = [];

        $specification->getWalker()->eachRef(function (RefInterface $attribute) use ($index, &$unresolved, &$dangling, &$ambiguous): void {
            $ref = $attribute->getRefValue();
            if (!is_string($ref)) {
                return;
            }

            if (str_starts_with($ref, '#/')) {
                if ($this->isDangling($ref, $index)) {
                    $dangling[$ref] = true;
                }

                return;
            }

            $key = $attribute->getRefKey() ?? new ComponentKey($ref);
            $bucket = $this->bucketFor($attribute);

            $pointer = $this->pointerForKey($key, $bucket, $index);
            if ($pointer !== null) {
                $attribute->setRef($pointer);
            } elseif (count($index->findAllByKey($key, $bucket)) > 1) {
                $ambiguous[(string) $key] = [$key, $bucket];
            } else {
                $unresolved[(string) $key] = true;
            }
        });

        foreach ($ambiguous as [$key, $bucket]) {
            $names = array_map(
                fn (AttributeInterface $component): string => ComponentName::of($component) ?? '(unnamed)',
                $index->findAllByKey($key, $bucket),
            );
            $this->logger?->warning("Ref: ambiguous reference '{$key}' — it names several {$bucket}: " . implode(', ', $names) . '; reference one by name');
        }

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
     * The bucket a ref on this attribute points into. A media type shortcut's ref is its schema's.
     */
    protected function bucketFor(AttributeInterface $attribute): string
    {
        return match (true) {
            $attribute instanceof OA\Response => 'responses',
            $attribute instanceof OA\Parameter => 'parameters',
            $attribute instanceof OA\RequestBody => 'requestBodies',
            $attribute instanceof OA\Header => 'headers',
            $attribute instanceof OA\Link => 'links',
            $attribute instanceof OA\Example => 'examples',
            $attribute instanceof OA\PathItem => 'pathItems',
            $attribute instanceof OA\Security\Scheme => 'securitySchemes',
            default => 'schemas',
        };
    }

    /**
     * The pointer to the one named component a key names in a bucket, if there is one.
     */
    protected function pointerForKey(ComponentKey $key, string $bucket, ComponentIndex $index): ?string
    {
        $component = $index->findByKey($key, $bucket);
        $name = $component instanceof AttributeInterface ? ComponentName::of($component) : null;

        return $name !== null ? JsonPointer::ref('components', $bucket, $name) : null;
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

    protected function resolveDiscriminatorMappings(Specification $specification, ComponentIndex $index): void
    {
        $specification->getWalker()->eachSchema(function (OA\Schema $schema) use ($index): void {
            if (!$schema->discriminator instanceof OA\Discriminator || $schema->discriminator->mapping === null) {
                return;
            }

            foreach ($schema->discriminator->mapping as $value => $type) {
                if (str_starts_with($type, '#/')) {
                    continue;
                }
                $pointer = $this->pointerForKey(new ComponentKey($type), 'schemas', $index);
                if ($pointer !== null) {
                    $schema->discriminator->mapping[$value] = $pointer;
                }
            }
        });
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

        $specification->getWalker()->eachRef(function (RefInterface $attribute) use (&$candidates): void {
            preg_match('/#\/components\/schemas\/([^\/]+)\/(properties\/.+)/', (string) $attribute->getRefValue(), $matches);

            if (count($matches) !== 3) {
                return;
            }

            // the captured name is escaped, `$candidates` is keyed by the raw one
            $name = JsonPointer::decode($matches[1]);
            $path = $matches[2];
            if (array_key_exists($name, $candidates)) {
                $index = $candidates[$name];
                // `$path` is copied through as captured — it is already-escaped tokens plus separators
                $attribute->setRef(JsonPointer::ref('components', 'schemas', $name, 'allOf', (string) $index) . '/' . $path);
            }
        });
    }

    /**
     * When allOf refs were added but the schema also has own properties, wrap them
     * in a dedicated allOf entry so the final output is a pure allOf composition.
     */
    protected function mergeAllOf(OA\Schema $schema): void
    {
        if ($schema->allOf === null || $schema->properties === null || $schema->properties === []) {
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
            $ref = $allOf->getRefValue();
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
