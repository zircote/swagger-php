<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\RefInterface;
use OpenApi\Contracts\TypeResolverAwareInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentKey;
use OpenApi\Type\TypeArguments;
use OpenApi\Type\TypeResolver;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Radebatz\TypeInfoExtras\Type\ClassLikeType;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\WrappingTypeInterface;

/**
 * Adds a schema for each set of type arguments a generic class is used with.
 *
 * A class declaring `@template T` and used as `Envelope<Customer>` would otherwise be referenced
 * as the plain `Envelope`, whose `T` says nothing. Here the class's schema is copied under the key
 * `Envelope` with the type arguments `Customer`, and the augmenters after this one name, fill and
 * reference the copy as they would any schema: `Names` names it, `Types` types its members with
 * `T` as `Customer`, and a use of `Envelope<Customer>` references it.
 *
 * The uses are what `Types` would reference from the attributes
 * `Specification\Walker::eachTypedAttribute()` lists, asked of `Types::wantedKeys()`, and a
 * `Schema\Ref` qualified with type arguments. A copy's members can use another copy
 * (`Envelope<Envelope<Customer>>`), so this runs until no use is left without a schema, to a
 * limit of nesting.
 *
 * The names a copy is built from are left on it as meta under this class, for the namer: the
 * explicit name of the copied schema, so `#[OA\Schema(component: 'Wrapper')]` gives
 * `Wrapper-of.Customer`, and the explicit names of the argument classes' schemas, so two classes
 * that share a short name but are named apart give copies named apart.
 *
 * A use with other qualifiers besides the type arguments, such as `groups`, copies the schema for
 * those qualifiers rather than the plain one.
 *
 * A generic class used without arguments, or with `mixed` only, keeps its plain schema. So does a
 * class without a schema of its own: there is nothing to copy. A use whose arguments cannot be
 * made a copy of, such as one with more arguments than the class has templates, is reported, and
 * references the plain schema too: `Refs` resolves a `Schema\Ref` whose arguments have no schema to
 * the plain one.
 *
 * @implements PipeInterface<Specification>
 */
class Generics implements PipeInterface, LoggerAwareInterface, TypeResolverAwareInterface
{
    /** how deep copies can ask for copies, so a class using itself with growing arguments ends */
    protected const MAX_DEPTH = 10;

    protected ?LoggerInterface $logger = null;

    /** asked which uses it would reference, so its rules are not repeated here */
    protected Types $types;

    /** run again after each round of copies, for the enums only a copy names */
    protected ImpliedSchemas $impliedSchemas;

    /** @var array<string, AttributeInterface> the first use asking for each key, by its string, for the location in a warning */
    protected array $uses = [];

    public function __construct(
        protected TypeResolver $typeResolver = new TypeResolver(),
    ) {
        $this->types = new Types($typeResolver);
        $this->impliedSchemas = new ImpliedSchemas($typeResolver);
    }

    public function __invoke(mixed $payload): mixed
    {
        $this->uses = [];
        $this->makeCopies($payload);

        return null;
    }

    public function setTypeResolver(TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;
        $this->types->setTypeResolver($typeResolver);
        $this->impliedSchemas->setTypeResolver($typeResolver);
        if ($this->logger instanceof LoggerInterface) {
            $this->typeResolver->setLogger($this->logger);
        }

        return $this;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->typeResolver->setLogger($logger);
        $this->impliedSchemas->setLogger($logger);
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * Makes a copy for every use that asks for one, round by round.
     */
    protected function makeCopies(Specification $specification): void
    {
        // keys that cannot be made, not worth another look
        $refused = [];
        $wanted = $this->wanted($specification);

        for ($depth = 0; $depth <= static::MAX_DEPTH; ++$depth) {
            // one index per round: a copy made in it changes no plain key
            $index = $specification->buildComponentIndex();
            $wanted = array_filter(
                array_diff_key($wanted, $refused),
                static fn (ComponentKey $key): bool => !$index->hasKey($key),
            );
            if ($wanted === []) {
                return;
            }

            if ($depth === static::MAX_DEPTH) {
                foreach ($wanted as $id => $key) {
                    $this->logger?->warning(sprintf('Type "%s" nests type arguments more than %d deep, using the plain schema%s', $this->describe($key), static::MAX_DEPTH, $this->at($id)));
                }

                return;
            }

            $copies = [];
            foreach ($wanted as $id => $key) {
                $copy = $this->specialise($specification, $key, $index, $id);
                if ($copy instanceof OA\Schema) {
                    $copies[] = $copy;
                } else {
                    $refused[$id] = true;
                }
            }
            if ($copies === []) {
                return;
            }

            // an enum only a copy names gets its schema before the copies' own uses are read
            ($this->impliedSchemas)($specification);
            $index = $specification->buildComponentIndex();

            // only a copy made in this round can use one that is not there yet
            $next = [];
            foreach ($copies as $copy) {
                foreach ($copy->properties ?? [] as $property) {
                    $this->collect($next, $property, $copy, $index);
                }
            }
            $wanted = $next;
        }
    }

    /**
     * Every key with type arguments that a use asks for.
     *
     * @return array<string, ComponentKey> by their string
     */
    protected function wanted(Specification $specification): array
    {
        $keys = [];
        $index = $specification->buildComponentIndex();

        // a ref written as a property's schema has no location of its own, so the property's is used
        /** @var \SplObjectStorage<AttributeInterface, AttributeInterface> $owners */
        $owners = new \SplObjectStorage();

        $walker = $specification->getWalker();
        $walker->eachTypedAttribute(function (OA\Property|OA\Parameter|OA\RequestBody $attribute, ?OA\Schema $schema) use (&$keys, $index, $owners): void {
            if (!$attribute instanceof OA\RequestBody && $attribute->schema instanceof RefInterface) {
                $owners[$attribute->schema] = $attribute;
            }
            $this->collect($keys, $attribute, $schema, $index);
        });
        $walker->eachRef(function (RefInterface $attribute) use (&$keys, $owners): void {
            $key = $attribute->getRefKey();
            // arguments that cannot be read are reported when the copy is asked for
            if ($key instanceof ComponentKey && isset($key->qualifiers[ComponentKey::TYPE_ARGUMENTS])) {
                $keys[(string) $key] = $key;
                $this->uses[(string) $key] ??= $owners->offsetExists($attribute) ? $owners[$attribute] : $attribute;
            }
        });

        return $keys;
    }

    /**
     * The keys `Types` would reference from a typed attribute, read in the component its schema
     * belongs to.
     *
     * @param array<string, ComponentKey> $keys
     */
    protected function collect(array &$keys, OA\Property|OA\Parameter|OA\RequestBody $attribute, ?OA\Schema $schema, ComponentIndex $index): void
    {
        foreach ($this->types->wantedKeys($attribute, $schema, $index) as $key) {
            $keys[(string) $key] = $key;
            $this->uses[(string) $key] ??= $attribute;
        }
    }

    /**
     * Copies the schema the key names without its type arguments, or returns null where there is
     * nothing to copy. That is the class's plain schema, or the one for the key's other
     * qualifiers, so `groups` keep their effect. The copy is named by `Names`, from the key and the
     * names left on it.
     */
    protected function specialise(Specification $specification, ComponentKey $key, ComponentIndex $index, string $id): ?OA\Schema
    {
        $base = $index->findAllByKey(new ComponentKey($key->class, array_diff_key($key->qualifiers, [ComponentKey::TYPE_ARGUMENTS => true])), 'schemas');
        if (count($base) !== 1 || !$base[0] instanceof OA\Schema) {
            return null;
        }

        // only a written-out ref can ask for arguments that bind nothing; a type cannot
        $given = $key->qualifiers[ComponentKey::TYPE_ARGUMENTS] ?? null;
        if (is_array($given)) {
            // a key sorts a list, so the order of the arguments is gone
            $this->logger?->warning(sprintf('Type arguments for "%s" are given as a list, using the plain schema%s; write them as one string, such as "%s"', $key->class, $this->at($id), implode(', ', $given)));

            return null;
        }

        $arguments = TypeArguments::of($key);
        if (!$arguments instanceof TypeArguments) {
            $this->logger?->warning(sprintf('Type "%s" has type arguments that cannot be read, using the plain schema%s; write each class with its namespace', $this->describe($key), $this->at($id)));

            return null;
        }

        $templates = $this->typeResolver->templateNames($key->class);
        if ($templates === []) {
            $this->logger?->warning(sprintf('Type "%s" has type arguments, but its class declares no @template, using the plain schema%s', $this->describe($key), $this->at($id)));

            return null;
        }

        // one argument too many would make a copy no different from the right number's
        if (count($arguments->types) !== count($templates)) {
            $this->logger?->warning(sprintf('Type "%s" does not match the templates "%s" of its class, using the plain schema%s', $this->describe($key), implode(', ', $templates), $this->at($id)));

            return null;
        }

        $copy = $base[0]->copy();
        $copy->setComponentKey($key);
        $copy->setMeta(static::class, array_filter([
            'base' => $copy->component,
            'arguments' => $this->argumentNames($arguments, $index),
        ]));
        $copy->component = null;
        $specification->add($copy);

        return $copy;
    }

    /**
     * The explicit names of the schemas of the classes in the arguments, by class. A class
     * without one is named as the namer names it anyway.
     *
     * @return array<string, string>
     */
    protected function argumentNames(TypeArguments $arguments, ComponentIndex $index): array
    {
        $names = [];
        foreach (array_unique($this->classes(...$arguments->types)) as $class) {
            $schemas = $index->findAllByKey(new ComponentKey($class), 'schemas');
            if (count($schemas) === 1 && $schemas[0] instanceof OA\Schema && is_string($schemas[0]->component)) {
                $names[$class] = $schemas[0]->component;
            }
        }

        return $names;
    }

    /**
     * The classes named anywhere in the types. Type-info's own `traverse()` does not reach a
     * generic type's arguments.
     *
     * @return list<string>
     */
    protected function classes(Type ...$types): array
    {
        $classes = [];
        foreach ($types as $type) {
            $classes = [...$classes, ...match (true) {
                $type instanceof ArrayShapeType => $this->classes(
                    ...array_column($type->getShape(), 'type'),
                    ...array_filter([$type->getExtraValueType()]),
                ),
                $type instanceof GenericType => $this->classes($type->getWrappedType(), ...$type->getVariableTypes()),
                $type instanceof WrappingTypeInterface => $this->classes($type->getWrappedType()),
                $type instanceof CompositeTypeInterface => $this->classes(...$type->getTypes()),
                $type instanceof ObjectType => [ltrim($type->getClassName(), '\\')],
                $type instanceof ClassLikeType => $this->classes($type->getObjectType()),
                default => [],
            }];
        }

        return $classes;
    }

    /**
     * Where the first use asking for a key is, for a log message, or nothing where that is unknown.
     */
    protected function at(string $id): string
    {
        $location = isset($this->uses[$id]) ? (string) $this->uses[$id]->getSourceLocation() : 'unknown';

        return $location === 'unknown' ? '' : ' in ' . $location;
    }

    /**
     * Name a key with type arguments for a log message, as it would be written: `Envelope<Customer>`.
     */
    protected function describe(ComponentKey $key): string
    {
        $arguments = $key->qualifiers[ComponentKey::TYPE_ARGUMENTS] ?? '';

        return $key->class . '<' . implode(', ', (array) $arguments) . '>';
    }
}
