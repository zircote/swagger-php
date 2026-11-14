<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Specification\ComponentKey;
use OpenApi\Type\Generics\TemplateBinder;
use OpenApi\Type\Generics\TemplateReader;
use OpenApi\Type\Generics\TypeArguments;
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
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;
use Symfony\Component\TypeInfo\TypeResolver\StringTypeResolver as SymfonyStringTypeResolver;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolverInterface;

/**
 * Resolves a PHP reflector to a SchemaType value object.
 *
 * Shared core between the spec-attributes augmenter and the classic annotation resolver.
 * Uses Symfony TypeInfo + phpstan/phpdoc-parser for reflective type resolution.
 *
 * A member can be read as seen from a component. In a component with type arguments, such as
 * `Envelope<Customer>`, a member reads each template as what `TemplateReader` says it stands for:
 * `T` is `Customer`. A member the class inherits or takes from a trait keeps its templates
 * unbound, with a warning.
 */
class TypeResolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected TypeMapper $typeMapper;

    /** @var \WeakMap<\Reflector, array{?SchemaType}> what each reflector resolved to, so a docblock is read and reported once */
    protected \WeakMap $resolved;

    /** @var \WeakMap<\Reflector, array<string, array{?SchemaType}|null>> what each reflector resolved to in a component with type arguments, by its class and arguments, null where the component binds nothing in it */
    protected \WeakMap $resolvedIn;

    /** @var \WeakMap<\Reflector, array{?Type}> each reflector's docblock type, so a docblock is parsed and reported once however many components read it */
    protected \WeakMap $docblockTypes;

    /** @var array<string, true> what was reported already about a component with type arguments, by what it is about */
    protected array $reported = [];

    protected TemplateReader $templateReader;

    protected TemplateBinder $templateBinder;

    /** Reuses parsed use statements when supported by symfony/type-info. */
    protected TypeContextFactory $docblockContexts;

    protected TypeContextFactory $reflectionContexts;

    protected TypeResolverInterface $docblockResolver;

    /**
     * @param TemplateReader|null $templateReader Tells what each template of a member stands for in a component with type arguments
     */
    public function __construct(?TemplateReader $templateReader = null)
    {
        $this->templateReader = $templateReader ?? new TemplateReader();
        $this->templateBinder = new TemplateBinder();
        $this->resolvedIn = new \WeakMap();
        $this->docblockTypes = new \WeakMap();
        $this->typeMapper = new TypeMapper();
        $this->docblockResolver = new StringTypeResolver();
        $this->resolved = new \WeakMap();
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
     * In a component with type arguments, the member's templates read as the arguments. A member
     * the arguments do not reach, and any member read without type arguments, is resolved as
     * `resolve()` resolves it. Each reflector is resolved once for each component.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     * @param ComponentKey|null                                                                   $component the key of the component the member is read in
     */
    public function resolveIn(\Reflector $reflector, ?ComponentKey $component): ?SchemaType
    {
        if (!$component?->typeArguments instanceof TypeArguments) {
            return $this->resolve($reflector);
        }

        // the qualifiers do not change what a template stands for
        $context = $component->class . '<' . $component->typeArguments . '>';
        $resolved = $this->resolvedIn[$reflector] ?? [];
        if (!array_key_exists($context, $resolved)) {
            if (!$this->templateReader->accepts($component->class, $component->typeArguments)) {
                $this->reportOnce('accepts ' . $context, sprintf('Type arguments of "%s" do not match the templates of the class, ignored', $context));
            }
            $bindings = $this->templateReader->bindings($reflector, $component->class, $component->typeArguments);
            if ($bindings === null) {
                $this->reportUnbound($reflector, $component->class, $component->typeArguments);
            }
            // null: the arguments do not reach the member
            $resolved[$context] = $bindings === null || $bindings === [] ? null : [$this->resolveBound($reflector, $bindings)];
            $this->resolvedIn[$reflector] = $resolved;
        }

        if ($resolved[$context] === null) {
            return $this->resolve($reflector);
        }
        [$type] = $resolved[$context];

        return $type instanceof SchemaType ? clone $type : null;
    }

    /**
     * The type a member's docblock gives it, or null where it gives none or one that cannot be read.
     *
     * The docblock is parsed and reported once for each reflector.
     */
    public function getDocblockType(\Reflector $reflector): ?Type
    {
        // wrapped, as the map reads a null value as missing
        $this->docblockTypes[$reflector] ??= [$this->readDocblockType($reflector)];

        return $this->docblockTypes[$reflector][0];
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

    protected function readDocblockType(\Reflector $reflector): ?Type
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
                    return $this->docblockResolver->resolve((string) $tagValue, $typeContext);
                } catch (\Throwable) {
                    // not only the exceptions the resolver declares: a docblock it fails on must not end the build
                    $this->logger?->warning(sprintf('Unsupported docblock type "%s" on %s', $tagValue->type, $this->describe($reflector)));
                }
            }
        }

        return null;
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
     * Report a member whose templates the type arguments do not reach, once for each component.
     */
    protected function reportUnbound(\Reflector $reflector, string $class, TypeArguments $typeArguments): void
    {
        $docblockType = $this->getDocblockType($reflector);
        if ($docblockType instanceof Type && $this->templateBinder->hasTemplate($docblockType)) {
            $this->reportOnce(
                $this->describe($reflector) . ' ' . $class . '<' . $typeArguments . '>',
                sprintf('Type arguments of "%s<%s>" do not reach an inherited or trait member, ignored in %s', $class, $typeArguments, SourceLocation::fromReflector($reflector)),
            );
        }
    }

    /**
     * Log a warning the first time it is reported for the given id.
     */
    protected function reportOnce(string $id, string $message): void
    {
        if (!isset($this->reported[$id])) {
            $this->reported[$id] = true;
            $this->logger?->warning($message);
        }
    }

    /**
     * Resolve with each template in the docblock type replaced by the type it is bound to.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     * @param array<string, Type>                                                                 $bindings  by template name
     */
    protected function resolveBound(\Reflector $reflector, array $bindings): ?SchemaType
    {
        $docblockType = $this->getDocblockType($reflector);
        $reflectionType = $this->getReflectionType($reflector);

        if ($docblockType instanceof Type) {
            try {
                $bound = $this->templateBinder->bind($docblockType, $bindings);
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

        return $this->toSchemaType($docblockType, $reflectionType);
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     */
    protected function doResolve(\Reflector $reflector): ?SchemaType
    {
        return $this->toSchemaType($this->getDocblockType($reflector), $this->getReflectionType($reflector));
    }

    /**
     * The schema type for a member's docblock type and its declared type. The docblock type is
     * the one mapped where there is one, and the member is nullable where either type is.
     */
    protected function toSchemaType(?Type $docblockType, ?Type $reflectionType): ?SchemaType
    {
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

        // `Foo<T>` on a class. OpenAPI can express nothing about the parameters, so the
        // type being parameterised is what is left, with the arguments kept beside it for a
        // class that is referenced. Last, so `array<...>` and `int<...>` reach the arms above.
        if ($type instanceof GenericType) {
            $result = $this->mapType($type->getWrappedType());
            if ($result->isRef()) {
                $result->typeArguments = new TypeArguments(...$type->getVariableTypes());
            }

            return $result;
        }

        return new SchemaType();
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
