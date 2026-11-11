<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Contracts\SourceInspectorInterface;
use OpenApi\Specification\ComponentKey;
use OpenApi\Utils\SourceInspector;
use OpenApi\Utils\SourceLocation;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Radebatz\TypeInfoExtras\Type\ExplicitType;
use Radebatz\TypeInfoExtras\Type\IntRangeType;
use Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver;
use Symfony\Component\TypeInfo\Exception\InvalidArgumentException;
use Symfony\Component\TypeInfo\Exception\UnsupportedException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\IntersectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\TemplateType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;
use Symfony\Component\TypeInfo\TypeResolver\StringTypeResolver as SymfonyStringTypeResolver;

/**
 * Resolves a PHP reflector to a SchemaType value object.
 *
 * Shared core between the spec-attributes augmenter and the classic annotation resolver.
 * Uses Symfony TypeInfo + phpstan/phpdoc-parser for reflective type resolution.
 *
 * A member is read as seen from a component. In a schema made for a generic class's type
 * arguments, such as `Envelope<Customer>`, a member the class declares itself reads each template
 * as its argument: `T` is `Customer`. Templates are matched to arguments by position, as PHPStan
 * and Psalm do. A template a method declares itself hides the class's of that name and stays
 * unbound. A member the class inherits or takes from a trait keeps its templates unbound, with a
 * warning, since the arguments of a parent or a trait are not read yet.
 */
class TypeResolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected TypeMapper $typeMapper;

    /** @var \WeakMap<\Reflector, array{?SchemaType}> what each reflector resolved to, so a docblock is read and reported once */
    protected \WeakMap $resolved;

    /** @var \WeakMap<\Reflector, array<string, array{?SchemaType}|null>> what each reflector resolved to in a component with type arguments, by the component, null where the component binds nothing in it */
    protected \WeakMap $resolvedIn;

    /** @var \WeakMap<\Reflector, array{?Type}> each reflector's docblock type, so a docblock is parsed and reported once however many components read it */
    protected \WeakMap $docblockTypes;

    /** @var array<string, list<string>> the templates each class declares, by class */
    protected array $templateNames = [];

    /** @var array<string, Type> what each template of the member being resolved reads as, by template name */
    protected array $bindings = [];

    /** @var array<string, true> the members already reported with templates left unbound, by member and component */
    protected array $unbound = [];

    protected SourceInspectorInterface $sourceInspector;

    /** Reuses parsed use statements when supported by symfony/type-info. */
    protected TypeContextFactory $docblockContexts;

    protected TypeContextFactory $reflectionContexts;

    /**
     * @param SourceInspectorInterface|null $sourceInspector Tells the members a class declares itself, whose templates are bound
     */
    public function __construct(?SourceInspectorInterface $sourceInspector = null)
    {
        $this->sourceInspector = $sourceInspector ?? new SourceInspector();
        $this->typeMapper = new TypeMapper();
        $this->resolved = new \WeakMap();
        $this->resolvedIn = new \WeakMap();
        $this->docblockTypes = new \WeakMap();
        $this->docblockContexts = new TypeContextFactory(new SymfonyStringTypeResolver());
        $this->reflectionContexts = new TypeContextFactory();
    }

    /**
     * Resolve the PHP type of a reflector into a SchemaType.
     *
     * A reflector is resolved once. Asking again gives a copy of the first answer, which the
     * caller can change freely.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     */
    public function resolve(\Reflector $reflector): ?SchemaType
    {
        // wrapped, as the map reads a null value as missing
        $this->resolved[$reflector] ??= [$this->doResolve($reflector)];
        [$resolved] = $this->resolved[$reflector];

        return $resolved instanceof SchemaType ? clone $resolved : null;
    }

    /**
     * Resolve the PHP type of a reflector as read in the component with the given key.
     *
     * In a schema made for type arguments, the member's templates read as the arguments. A
     * member the arguments do not reach, and any member read without type arguments, is resolved
     * as `resolve()` resolves it. Each reflector is resolved once for each component.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     * @param ComponentKey|null                                                                   $component the key of the component the member is read in
     */
    public function resolveIn(\Reflector $reflector, ?ComponentKey $component): ?SchemaType
    {
        if (!$component instanceof ComponentKey || !isset($component->qualifiers[ComponentKey::TYPE_ARGUMENTS])) {
            return $this->resolve($reflector);
        }

        $context = (string) $component;
        $resolved = $this->resolvedIn[$reflector] ?? [];
        if (!array_key_exists($context, $resolved)) {
            $bindings = $this->findBindings($reflector, $component);
            // null: the arguments do not reach the member
            $resolved[$context] = $bindings === [] ? null : [$this->doResolveBound($reflector, $bindings)];
            $this->resolvedIn[$reflector] = $resolved;
        }

        if ($resolved[$context] === null) {
            return $this->resolve($reflector);
        }
        [$type] = $resolved[$context];

        return $type instanceof SchemaType ? clone $type : null;
    }

    /**
     * The templates a class declares, in declaration order.
     *
     * @return list<string>
     */
    public function templateNames(string $class): array
    {
        if (!class_exists($class) && !interface_exists($class)) {
            return [];
        }

        return $this->templateNames[$class] ??= array_keys($this->docblockContexts->createFromClassName($class)->templates);
    }

    public function getDocblockType(\Reflector $reflector): ?Type
    {
        $docComment = match (true) {
            $reflector instanceof \ReflectionProperty => $reflector->isPromoted()
                && $reflector->getDeclaringClass()->getConstructor() instanceof \ReflectionMethod
                    ? $reflector->getDeclaringClass()->getConstructor()->getDocComment()
                    : $reflector->getDocComment(),
            $reflector instanceof \ReflectionParameter => $reflector->getDeclaringFunction()->getDocComment(),
            $reflector instanceof \ReflectionFunctionAbstract => $reflector->getDocComment(),
            default => null,
        };

        if (!$docComment) {
            return null;
        }

        $typeContext = $this->docblockContexts->createFromReflection($reflector);

        $tagName = match (true) {
            $reflector instanceof \ReflectionProperty => $reflector->isPromoted()
                ? '@param'
                : '@var',
            $reflector instanceof \ReflectionParameter => '@param',
            $reflector instanceof \ReflectionFunctionAbstract => '@return',
            default => null,
        };

        if ($tagName === null) {
            return null;
        }

        $docNode = $this->parseDocComment($docComment);

        foreach ($docNode->getTagsByName($tagName) as $tag) {
            $tagValue = $tag->value;

            if (
                $tagValue instanceof VarTagValueNode
                || ($tagValue instanceof ParamTagValueNode && '$' . $reflector->getName() === $tagValue->parameterName)
                || $tagValue instanceof ReturnTagValueNode
            ) {
                try {
                    return (new StringTypeResolver())->resolve((string) $tagValue, $typeContext);
                } catch (UnsupportedException|InvalidArgumentException) {
                    $this->logger?->warning(sprintf('Unsupported docblock type "%s" on %s', $tagValue->type, $this->describe($reflector)));
                }
            }
        }

        return null;
    }

    /**
     * Name a member for a log message: `Pet::$name`, `Pet::name()`, `$id of Pet::get()`.
     */
    public function describe(\Reflector $reflector): string
    {
        return match (true) {
            $reflector instanceof \ReflectionProperty => $reflector->class . '::$' . $reflector->name,
            $reflector instanceof \ReflectionParameter => '$' . $reflector->name . ' of ' . $this->describe($reflector->getDeclaringFunction()),
            $reflector instanceof \ReflectionMethod => $reflector->class . '::' . $reflector->name . '()',
            $reflector instanceof \ReflectionFunctionAbstract => $reflector->name . '()',
            default => $reflector::class,
        };
    }

    /**
     * The type each template stands for in the component, by template name. None where the key
     * carries no type arguments or not one for each template, as `Generics` makes no schema for
     * those, or where the member is not the key's class's own.
     *
     * @return array<string, Type>
     */
    protected function findBindings(\Reflector $reflector, ComponentKey $component): array
    {
        $arguments = TypeArguments::of($component);
        if (!$arguments instanceof TypeArguments) {
            return [];
        }

        $names = $this->templateNames($component->class);
        if ($names === [] || count($names) !== count($arguments->types)) {
            return [];
        }

        if ($this->declares($component->class, $reflector)) {
            // a method's own templates hide the class's of the same name
            return array_diff_key(array_combine($names, $arguments->types), array_flip($this->ownTemplateNames($reflector)));
        }

        $docblockType = $this->docblockType($reflector);
        $id = $this->describe($reflector) . ' ' . $component;
        if ($docblockType instanceof Type && $this->mentionsTemplate($docblockType) && !isset($this->unbound[$id])) {
            $this->unbound[$id] = true;
            $this->logger?->warning(sprintf('Type arguments of "%s<%s>" do not reach an inherited or trait member, ignored in %s', $component->class, $arguments, SourceLocation::fromReflector($reflector)));
        }

        return [];
    }

    /**
     * The templates the method whose docblock types the member declares itself: a method's, a
     * parameter's method's, or a promoted property's constructor's.
     *
     * @return list<string>
     */
    protected function ownTemplateNames(\Reflector $reflector): array
    {
        $function = match (true) {
            $reflector instanceof \ReflectionFunctionAbstract => $reflector,
            $reflector instanceof \ReflectionParameter => $reflector->getDeclaringFunction(),
            $reflector instanceof \ReflectionProperty && $reflector->isPromoted() => $reflector->getDeclaringClass()->getConstructor(),
            default => null,
        };
        $docComment = $function?->getDocComment();
        if (!is_string($docComment)) {
            return [];
        }

        $docNode = $this->parseDocComment($docComment);
        $names = [];
        foreach (['@template', '@phpstan-template', '@psalm-template'] as $tagName) {
            foreach ($docNode->getTemplateTagValues($tagName) as $template) {
                $names[] = $template->name;
            }
        }

        return $names;
    }

    protected function parseDocComment(string $docComment): PhpDocNode
    {
        $config = new ParserConfig([]);
        $constExprParser = new ConstExprParser($config);
        $phpDocParser = new PhpDocParser(
            $config,
            new TypeParser($config, $constExprParser),
            $constExprParser,
        );

        return $phpDocParser->parse(new TokenIterator((new Lexer($config))->tokenize($docComment)));
    }

    /**
     * The docblock type, read once for each reflector.
     */
    protected function docblockType(\Reflector $reflector): ?Type
    {
        // wrapped, as the map reads a null value as missing
        $this->docblockTypes[$reflector] ??= [$this->getDocblockType($reflector)];

        return $this->docblockTypes[$reflector][0];
    }

    /**
     * Whether the member is written in the class's own body, not inherited or taken from a trait.
     */
    protected function declares(string $class, \Reflector $reflector): bool
    {
        $member = $reflector instanceof \ReflectionParameter ? $reflector->getDeclaringFunction() : $reflector;
        if (!$member instanceof \ReflectionMethod && !$member instanceof \ReflectionProperty && !$member instanceof \ReflectionClassConstant) {
            return false;
        }

        $declaringClass = $member->getDeclaringClass();

        return $declaringClass->getName() === $class && $this->sourceInspector->declares($declaringClass, $member);
    }

    /**
     * Resolve with the templates bound, as `doResolve()` reads them from `$bindings`.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     * @param array<string, Type>                                                                 $bindings  by template name
     */
    protected function doResolveBound(\Reflector $reflector, array $bindings): ?SchemaType
    {
        $this->bindings = $bindings;
        try {
            return $this->doResolve($reflector);
        } finally {
            $this->bindings = [];
        }
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     */
    protected function doResolve(\Reflector $reflector): ?SchemaType
    {
        $docblockType = $this->docblockType($reflector);
        $reflectionType = $this->getReflectionType($reflector);

        if ($docblockType instanceof Type && $this->bindings !== []) {
            try {
                $bound = $this->bindTemplates($docblockType, $this->bindings);
            } catch (InvalidArgumentException $exception) {
                // an argument the type cannot take, such as `int` in `T&\Countable`
                $this->logger?->warning(sprintf('Type "%s" cannot take the type arguments, omitted in %s: %s', $docblockType, SourceLocation::fromReflector($reflector), $exception->getMessage()));
                $bound = Type::mixed();
            }
            // `mixed` is all PHP can declare for a template, and its `null` is not the argument's
            if ((string) $bound !== (string) $docblockType && (string) $reflectionType === 'mixed') {
                $reflectionType = null;
            }
            $docblockType = $bound;
        }

        if (!$docblockType && !$reflectionType) {
            return null;
        }

        $nullable = null;
        if (($docblockType instanceof Type && $docblockType->isNullable()) || ($reflectionType instanceof Type && $reflectionType->isNullable())) {
            $nullable = true;
        }

        $docblockType = $docblockType instanceof NullableType ? $docblockType->getWrappedType() : $docblockType;
        $reflectionType = $reflectionType instanceof NullableType ? $reflectionType->getWrappedType() : $reflectionType;

        $effectiveType = $docblockType ?? $reflectionType;
        if (!$effectiveType instanceof Type) {
            return $nullable !== null ? new SchemaType(nullable: $nullable) : null;
        }

        $result = $this->mapType($effectiveType);
        $result->nullable = $nullable;

        $this->applyNativeTypeMapping($result);

        return $result;
    }

    protected function mapType(Type $type): SchemaType
    {
        if ($type instanceof CompositeTypeInterface) {
            return $this->mapCompositeType($type);
        }

        if ($type instanceof BuiltinType) {
            return $this->mapBuiltinType($type);
        }

        if ($type instanceof ObjectType) {
            // In the global namespace a short-name docblock comes back as `\Foo`, while
            // `\Foo` itself comes back as `Foo`. `ComponentIndex` keys on `getClassName()`,
            // which carries no leading slash.
            $className = ltrim($type->getClassName(), '\\');

            if ($className === \stdClass::class) {
                return new SchemaType(type: 'object');
            }

            // the date classes themselves are mapped by name, a subclass of them by what it is
            if (is_a($className, \DateTimeInterface::class, true)) {
                return new SchemaType(type: 'string', format: 'date-time');
            }

            return new SchemaType(type: $className);
        }

        if ($type instanceof IntRangeType) {
            // an open end comes as `PHP_INT_MIN`/`PHP_INT_MAX`, which is no bound of the API
            return new SchemaType(
                type: $type->getTypeIdentifier()->value,
                minimum: \PHP_INT_MIN === $type->getFrom() ? null : $type->getFrom(),
                maximum: \PHP_INT_MAX === $type->getTo() ? null : $type->getTo(),
            );
        }

        if ($type instanceof ExplicitType) {
            return new SchemaType(type: $type->getTypeIdentifier()->value);
        }

        if ($type instanceof ArrayShapeType && [] !== $type->getShape()) {
            return $this->mapArrayShape($type);
        }

        if ($type instanceof CollectionType) {
            return $this->mapCollectionType($type);
        }

        // `Foo<T>` on a class: the class, with its arguments kept where the class declares
        // templates for them to fill, so the use can reference a schema made for them. Last,
        // so `array<...>` and `int<...>` reach the arms above.
        if ($type instanceof GenericType) {
            $result = $this->mapType($type->getWrappedType());
            if ($result->isRef() && $this->templateNames($result->type) !== []) {
                $result->typeArguments = $this->typeArguments($type);
            }

            return $result;
        }

        return new SchemaType();
    }

    /**
     * The arguments, or null when every one is `mixed`, which says nothing a template does not,
     * or when one is still a template, as in the class's own `Node<T>`.
     *
     * @param GenericType<ObjectType<class-string>> $type
     */
    protected function typeArguments(GenericType $type): ?TypeArguments
    {
        $arguments = $type->getVariableTypes();
        $mixed = array_filter($arguments, static fn (Type $argument): bool => (string) $argument === 'mixed');
        $templates = array_filter($arguments, $this->mentionsTemplate(...));

        return count($mixed) === count($arguments) || $templates !== [] ? null : new TypeArguments(...$arguments);
    }

    /**
     * Whether a template occurs anywhere in the type. Type-info's own `traverse()` does not reach
     * a generic type's arguments.
     */
    protected function mentionsTemplate(Type $type): bool
    {
        return match (true) {
            $type instanceof TemplateType => true,
            $type instanceof GenericType => array_filter($type->getVariableTypes(), $this->mentionsTemplate(...)) !== [],
            $type instanceof CollectionType, $type instanceof NullableType => $this->mentionsTemplate($type->getWrappedType()),
            $type instanceof CompositeTypeInterface => array_filter($type->getTypes(), $this->mentionsTemplate(...)) !== [],
            default => false,
        };
    }

    /**
     * The type with each template replaced by its argument. Type-info has no helper for this, so
     * the type tree is rebuilt wherever a template occurs in it.
     *
     * @param array<string, Type> $bindings
     */
    protected function bindTemplates(Type $type, array $bindings): Type
    {
        return match (true) {
            $type instanceof TemplateType => $bindings[$type->getName()] ?? $type,
            $type instanceof NullableType => Type::nullable($this->bindTemplates($type->getWrappedType(), $bindings)),
            $type instanceof ArrayShapeType => $this->bindArrayShape($type, $bindings),
            $type instanceof CollectionType => new CollectionType(
                $type->getWrappedType() instanceof GenericType ? $this->bindGeneric($type->getWrappedType(), $bindings) : $type->getWrappedType(),
                $type->isList(),
            ),
            $type instanceof GenericType => $this->bindGeneric($type, $bindings),
            $type instanceof UnionType => $this->bindUnion($type, $bindings),
            $type instanceof IntersectionType => Type::intersection(...array_map(fn (Type $member): Type => $this->bindTemplates($member, $bindings), $type->getTypes())),
            default => $type,
        };
    }

    /**
     * An array shape with each template replaced. It extends a collection, whose rebuild would
     * drop the shape.
     *
     * @param array<string, Type> $bindings
     */
    protected function bindArrayShape(ArrayShapeType $type, array $bindings): ArrayShapeType
    {
        $shape = array_map(fn (array $member): array => ['type' => $this->bindTemplates($member['type'], $bindings), 'optional' => $member['optional']], $type->getShape());
        $extraValueType = $type->getExtraValueType();

        return new ArrayShapeType(
            $shape,
            $type->getExtraKeyType(),
            $extraValueType instanceof Type ? $this->bindTemplates($extraValueType, $bindings) : null,
        );
    }

    /**
     * A union with each template replaced. One that takes `mixed` is `mixed`, which type-info does
     * not allow in a union.
     *
     * @param UnionType<Type>     $type
     * @param array<string, Type> $bindings
     */
    protected function bindUnion(UnionType $type, array $bindings): Type
    {
        $members = array_map(fn (Type $member): Type => $this->bindTemplates($member, $bindings), $type->getTypes());
        foreach ($members as $member) {
            if ($member->isIdentifiedBy(TypeIdentifier::MIXED)) {
                return Type::mixed();
            }
        }

        return Type::union(...$members);
    }

    /**
     * @template T of BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>
     *
     * @param GenericType<T>      $type
     * @param array<string, Type> $bindings
     *
     * @return GenericType<T>
     */
    protected function bindGeneric(GenericType $type, array $bindings): GenericType
    {
        return new GenericType($type->getWrappedType(), ...array_map(fn (Type $variable): Type => $this->bindTemplates($variable, $bindings), $type->getVariableTypes()));
    }

    /**
     * @param CompositeTypeInterface<Type> $type
     */
    protected function mapCompositeType(CompositeTypeInterface $type): SchemaType
    {
        $types = $type->getTypes();

        $isNonZeroInt = 2 === count($types) && $types[0] instanceof IntRangeType && $types[1] instanceof IntRangeType;
        if ($isNonZeroInt) {
            return new SchemaType(type: 'int', not: ['const' => 0]);
        }

        $allBuiltin = array_reduce($types, static fn ($carry, $t): bool => $carry && $t instanceof BuiltinType, true);

        if ($type instanceof UnionType) {
            if ($allBuiltin) {
                $mappableTypes = array_values(array_filter(
                    array_map(static fn (Type $t): string => (string) $t, $types),
                    $this->typeMapper->hasOpenApiType(...),
                ));

                return new SchemaType(type: [] === $mappableTypes ? null : $mappableTypes);
            }

            $builtinTypes = array_filter($types, static fn (Type $t): bool => $t instanceof BuiltinType);
            $otherTypes = array_filter($types, static fn (Type $t): bool => !$t instanceof BuiltinType);

            $oneOf = [];
            if ($builtinTypes !== []) {
                $builtinSchema = new SchemaType(
                    type: array_values(array_map(static fn (Type $t): string => (string) $t, $builtinTypes)),
                );
                $this->applyNativeTypeMapping($builtinSchema);
                $oneOf[] = $builtinSchema;
            }

            foreach ($otherTypes as $otherType) {
                $oneOf[] = $this->mapType($otherType);
            }

            return new SchemaType(oneOf: $oneOf);
        }

        if ($type instanceof IntersectionType) {
            $allOf = [];
            foreach ($types as $intersectionType) {
                $allOf[] = $this->mapType($intersectionType);
            }

            return new SchemaType(allOf: $allOf);
        }

        return new SchemaType();
    }

    /**
     * @param BuiltinType<TypeIdentifier> $type
     */
    protected function mapBuiltinType(BuiltinType $type): SchemaType
    {
        $typeName = (string) $type;
        if ($this->typeMapper->hasOpenApiType($typeName)) {
            return new SchemaType(type: $typeName);
        }

        return new SchemaType();
    }

    /**
     * @param CollectionType<BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>|GenericType<BuiltinType<TypeIdentifier::ARRAY>|BuiltinType<TypeIdentifier::ITERABLE>|ObjectType<class-string>>> $type
     */
    protected function mapCollectionType(CollectionType $type): SchemaType
    {
        if ($type->isList() || $type->getCollectionKeyType() instanceof UnionType) {
            $itemType = $this->mapType($type->getCollectionValueType());
            $this->applyNativeTypeMapping($itemType);

            return new SchemaType(type: 'array', items: $itemType);
        }

        $valueSchema = $this->mapType($type->getCollectionValueType());
        $this->applyNativeTypeMapping($valueSchema);

        return new SchemaType(type: 'object', additionalProperties: $valueSchema);
    }

    protected function mapArrayShape(ArrayShapeType $type): SchemaType
    {
        $shape = $type->getShape();

        if (array_is_list($shape)) {
            $itemType = $this->mapType($type->getCollectionValueType());
            $this->applyNativeTypeMapping($itemType);

            return new SchemaType(type: 'array', items: $itemType);
        }

        $properties = [];
        $required = [];
        foreach ($shape as $name => $member) {
            $propertyName = (string) $name;
            $propertySchema = $this->mapType($member['type']);
            $this->applyNativeTypeMapping($propertySchema);
            $properties[$propertyName] = $propertySchema;

            if (!($member['optional'] ?? false)) {
                $required[] = $propertyName;
            }
        }

        $result = new SchemaType(type: 'object', properties: $properties);
        if ([] !== $required) {
            $result->required = $required;
        }

        if ($type->getExtraValueType() instanceof Type) {
            $result->additionalProperties = true;
        }

        return $result;
    }

    /**
     * Apply native PHP type to OpenAPI type/format mapping in-place.
     */
    protected function applyNativeTypeMapping(SchemaType $schema): void
    {
        if (is_string($schema->type)) {
            $mapped = $this->typeMapper->map($schema->type);
            if ($mapped === null) {
                // not a native type — leave as-is (likely a FQCN for ref resolution)
            } elseif ('mixed' === $mapped['type']) {
                $schema->type = null;
            } else {
                $schema->type = $mapped['type'];
                if ($mapped['format'] !== null && $schema->format === null) {
                    $schema->format = $mapped['format'];
                }
            }
        } elseif (is_array($schema->type)) {
            /** @var list<string> $mapped neither array_map() nor toSpecTypes() change a list's shape */
            $mapped = $this->typeMapper->toSpecTypes(
                array_map(static fn ($t): string => strtolower((string) $t), $schema->type),
            );
            $schema->type = $mapped;
        }
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClass<object>|\ReflectionClassConstant $reflector
     */
    protected function getReflectionType(\Reflector $reflector): ?Type
    {
        $subject = $reflector instanceof \ReflectionClass
            ? $reflector->getName()
            : (
                $reflector instanceof \ReflectionMethod
                ? $reflector->getReturnType()
                : (method_exists($reflector, 'getType') ? $reflector->getType() : null)
            );

        try {
            $typeContext = $this->reflectionContexts->createFromReflection($reflector);

            return (new ReflectionTypeResolver())->resolve($subject, $typeContext);
        } catch (UnsupportedException) {
            return null;
        }
    }
}
