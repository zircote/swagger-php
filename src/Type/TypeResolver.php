<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

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
 */
class TypeResolver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected TypeMapper $typeMapper;

    /** @var \WeakMap<\Reflector, array{?SchemaType}> what each reflector resolved to, so a docblock is read and reported once */
    protected \WeakMap $resolved;

    /** Reuses parsed use statements when supported by symfony/type-info. */
    protected TypeContextFactory $docblockContexts;

    /** The contexts of a member's declared PHP type. */
    protected TypeContextFactory $declaredTypeContexts;

    protected TypeResolverInterface $docblockResolver;

    protected TypeResolverInterface $declaredTypeResolver;

    protected Lexer $docblockLexer;

    protected PhpDocParser $docblockParser;

    public function __construct()
    {
        $config = new ParserConfig([]);
        $constExprParser = new ConstExprParser($config);
        $this->docblockLexer = new Lexer($config);
        $this->docblockParser = new PhpDocParser($config, new TypeParser($config, $constExprParser), $constExprParser);

        $this->typeMapper = new TypeMapper();
        $this->docblockResolver = new StringTypeResolver();
        $this->declaredTypeResolver = new ReflectionTypeResolver();
        $this->resolved = new \WeakMap();
        $this->docblockContexts = new TypeContextFactory(new SymfonyStringTypeResolver());
        $this->declaredTypeContexts = new TypeContextFactory();
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

    public function getDocblockType(\Reflector $reflector): ?Type
    {
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

        $typeContext = null;
        foreach ($this->parseDocComment($docComment)->getTagsByName($tagName) as $tag) {
            $tagValue = $tag->value;

            if (
                $tagValue instanceof VarTagValueNode
                || ($tagValue instanceof ParamTagValueNode && '$' . $reflector->getName() === $tagValue->parameterName)
                || $tagValue instanceof ReturnTagValueNode
            ) {
                $typeContext ??= $this->docblockContexts->createFromReflection($reflector);

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

    protected function parseDocComment(string $docComment): PhpDocNode
    {
        return $this->docblockParser->parse(new TokenIterator($this->docblockLexer->tokenize($docComment)));
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     */
    protected function doResolve(\Reflector $reflector): ?SchemaType
    {
        return $this->toSchemaType($this->getDocblockType($reflector), $this->getReflectionType($reflector));
    }

    /**
     * The schema type for a member's docblock type and its declared type.
     *
     * The docblock type is the one mapped where there is one, and the member is nullable where
     * either type is.
     */
    protected function toSchemaType(?Type $docblockType, ?Type $declaredType): ?SchemaType
    {
        if (!$docblockType && !$declaredType) {
            return null;
        }

        $nullable = ($docblockType?->isNullable() || $declaredType?->isNullable()) ? true : null;

        $docblockType = $docblockType instanceof NullableType ? $docblockType->getWrappedType() : $docblockType;
        $declaredType = $declaredType instanceof NullableType ? $declaredType->getWrappedType() : $declaredType;

        $effectiveType = $docblockType ?? $declaredType;
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
            return $this->mapExplicitType($type);
        }

        if ($type instanceof ArrayShapeType && [] !== $type->getShape()) {
            return $this->mapArrayShape($type);
        }

        if ($type instanceof CollectionType) {
            return $this->mapCollectionType($type);
        }

        // `Foo<T>` on a class. OpenAPI can express nothing about the parameters, so the
        // type being parameterised is what is left. Last, so `array<...>` and `int<...>`
        // reach the arms above.
        if ($type instanceof GenericType) {
            return $this->mapType($type->getWrappedType());
        }

        return new SchemaType();
    }

    /**
     * A string pseudo-type that rules out the empty string gets `minLength: 1`.
     *
     * The others, such as `numeric-string` or `lowercase-string`, map to their builtin type.
     *
     * @param ExplicitType<TypeIdentifier> $type
     */
    protected function mapExplicitType(ExplicitType $type): SchemaType
    {
        $schemaType = new SchemaType(type: $type->getTypeIdentifier()->value);

        $name = $type->getExplicitType();
        if ($type->isIdentifiedBy(TypeIdentifier::STRING) && (str_starts_with($name, 'non-empty-') || in_array($name, ['non-falsy-string', 'truthy-string'], true))) {
            $schemaType->minLength = 1;
        }

        return $schemaType;
    }

    /**
     * @param CompositeTypeInterface<Type> $type
     */
    protected function mapCompositeType(CompositeTypeInterface $type): SchemaType
    {
        $types = $type->getTypes();

        if ($this->isNonZeroInt($types)) {
            return new SchemaType(type: 'int', not: ['const' => 0]);
        }

        if ($type instanceof UnionType) {
            $builtinTypes = array_filter($types, static fn (Type $part): bool => $part instanceof BuiltinType);
            $otherTypes = array_filter($types, static fn (Type $part): bool => !$part instanceof BuiltinType);

            if ($otherTypes === []) {
                $mappableTypes = array_values(array_filter(
                    array_map(static fn (Type $part): string => (string) $part, $types),
                    $this->typeMapper->hasOpenApiType(...),
                ));

                return new SchemaType(type: [] === $mappableTypes ? null : $mappableTypes);
            }

            $members = [];
            if ($builtinTypes !== []) {
                $builtinSchema = new SchemaType(
                    type: array_values(array_map(static fn (Type $part): string => (string) $part, $builtinTypes)),
                );
                $this->applyNativeTypeMapping($builtinSchema);
                $members[] = $builtinSchema;
            }

            foreach ($otherTypes as $otherType) {
                $members[] = $this->mapType($otherType);
            }

            // integer ranges may overlap or touch, and a value in two of them would fail `oneOf`
            $intRanges = array_filter($otherTypes, static fn (Type $part): bool => $part instanceof IntRangeType);
            if (count($intRanges) > 1) {
                array_walk($members, $this->applyNativeTypeMapping(...));

                return new SchemaType(anyOf: $members);
            }

            return new SchemaType(oneOf: $members);
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
     * Whether a union's parts are exactly `int<min, -1>` and `int<1, max>`, in either order.
     *
     * @param list<Type> $types
     */
    protected function isNonZeroInt(array $types): bool
    {
        if (2 !== count($types) || !$types[0] instanceof IntRangeType || !$types[1] instanceof IntRangeType) {
            return false;
        }

        $bounds = array_map(static fn (IntRangeType $range): array => [$range->getFrom(), $range->getTo()], $types);
        sort($bounds);

        return [[\PHP_INT_MIN, -1], [1, \PHP_INT_MAX]] === $bounds;
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
            return new SchemaType(type: 'array', items: $this->mapNestedType($type->getCollectionValueType()));
        }

        return new SchemaType(type: 'object', additionalProperties: $this->mapNestedType($type->getCollectionValueType()));
    }

    protected function mapArrayShape(ArrayShapeType $type): SchemaType
    {
        $shape = $type->getShape();

        if (array_is_list($shape)) {
            return new SchemaType(type: 'array', items: $this->mapNestedType($type->getCollectionValueType()));
        }

        $properties = [];
        $required = [];
        foreach ($shape as $name => $member) {
            $propertyName = (string) $name;
            $properties[$propertyName] = $this->mapNestedType($member['type']);

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
     * A type nested in another, an item, a map value or a shape's property, mapped to its OpenAPI type.
     */
    protected function mapNestedType(Type $type): SchemaType
    {
        $schemaType = $this->mapType($type);
        $this->applyNativeTypeMapping($schemaType);

        return $schemaType;
    }

    /**
     * Apply native PHP type to OpenAPI type/format mapping in-place.
     */
    protected function applyNativeTypeMapping(SchemaType $schema): void
    {
        if (is_array($schema->type)) {
            /** @var list<string> $mapped neither array_map() nor toSpecTypes() change a list's shape */
            $mapped = $this->typeMapper->toSpecTypes(
                array_map(static fn ($part): string => strtolower((string) $part), $schema->type),
            );
            $schema->type = $mapped;

            return;
        }

        $mapped = is_string($schema->type) ? $this->typeMapper->map($schema->type) : null;
        if ($mapped === null) {
            // not a native type, and most likely a class name for the ref resolution
            return;
        }

        if ('mixed' === $mapped['type']) {
            $schema->type = null;

            return;
        }

        $schema->type = $mapped['type'];
        if ($mapped['format'] !== null && $schema->format === null) {
            $schema->format = $mapped['format'];
        }
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClass<object>|\ReflectionClassConstant $reflector
     */
    protected function getReflectionType(\Reflector $reflector): ?Type
    {
        $subject = match (true) {
            $reflector instanceof \ReflectionClass => $reflector->getName(),
            $reflector instanceof \ReflectionMethod => $reflector->getReturnType(),
            default => method_exists($reflector, 'getType') ? $reflector->getType() : null,
        };

        try {
            return $this->declaredTypeResolver->resolve($subject, $this->declaredTypeContexts->createFromReflection($reflector));
        } catch (UnsupportedException) {
            return null;
        }
    }
}
