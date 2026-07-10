<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Utils\TypeMapper;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use Radebatz\TypeInfoExtras\Type\ExplicitType;
use Radebatz\TypeInfoExtras\Type\IntRangeType;
use Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver;
use Symfony\Component\TypeInfo\Exception\UnsupportedException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\IntersectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;

/**
 * Resolves a PHP reflector to a SchemaType value object.
 *
 * Shared core between the spec-attributes augmenter and the classic annotation resolver.
 * Uses Symfony TypeInfo + phpstan/phpdoc-parser for reflective type resolution.
 */
class TypeResolver
{
    protected TypeMapper $typeMapper;

    public function __construct()
    {
        $this->typeMapper = new TypeMapper();
    }

    /**
     * Resolve the PHP type of a reflector into a SchemaType.
     *
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClassConstant $reflector
     */
    public function resolve(\Reflector $reflector): ?SchemaType
    {
        $docblockType = $this->getDocblockType($reflector);
        $reflectionType = $this->getReflectionType($reflector);

        if (!$docblockType && !$reflectionType) {
            return null;
        }

        $nullable = null;
        if (($docblockType && $docblockType->isNullable()) || ($reflectionType && $reflectionType->isNullable())) {
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
            return new SchemaType(type: $type->getClassName());
        }

        if ($type instanceof IntRangeType) {
            return new SchemaType(
                type: $type->getTypeIdentifier()->value,
                minimum: $type->getFrom(),
                maximum: $type->getTo(),
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

        return new SchemaType();
    }

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

    protected function mapBuiltinType(BuiltinType $type): SchemaType
    {
        $typeName = (string) $type;
        if ($this->typeMapper->hasOpenApiType($typeName)) {
            return new SchemaType(type: $typeName);
        }

        return new SchemaType();
    }

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
            $schema->type = $this->typeMapper->toSpecTypes(
                array_map(static fn ($t): string => strtolower((string) $t), $schema->type),
            );
        }
    }

    /**
     * @param \ReflectionProperty|\ReflectionParameter|\ReflectionMethod|\ReflectionClass $reflector
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
            $typeContext = (new TypeContextFactory())->createFromReflection($reflector);

            return (new ReflectionTypeResolver())->resolve($subject, $typeContext);
        } catch (UnsupportedException) {
            return null;
        }
    }

    public function getDocblockType(\Reflector $reflector): ?Type
    {
        $docComment = match (true) {
            $reflector instanceof \ReflectionProperty => $reflector->isPromoted()
                && $reflector->getDeclaringClass() && $reflector->getDeclaringClass()->getConstructor()
                    ? $reflector->getDeclaringClass()->getConstructor()->getDocComment()
                    : $reflector->getDocComment(),
            $reflector instanceof \ReflectionParameter => $reflector->getDeclaringFunction()->getDocComment(),
            $reflector instanceof \ReflectionFunctionAbstract => $reflector->getDocComment(),
            default => null,
        };

        if (!$docComment) {
            return null;
        }

        $typeContext = (new TypeContextFactory())->createFromReflection($reflector);

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

        $lexer = new Lexer(new ParserConfig([]));
        $config = new ParserConfig([]);
        $constExprParser = new ConstExprParser($config);
        $phpDocParser = new PhpDocParser(
            $config,
            new TypeParser($config, $constExprParser),
            $constExprParser,
        );

        $tokens = new TokenIterator($lexer->tokenize($docComment));
        $docNode = $phpDocParser->parse($tokens);

        foreach ($docNode->getTagsByName($tagName) as $tag) {
            $tagValue = $tag->value;

            if (
                $tagValue instanceof VarTagValueNode
                || ($tagValue instanceof ParamTagValueNode && '$' . $reflector->getName() === $tagValue->parameterName)
                || $tagValue instanceof ReturnTagValueNode
            ) {
                try {
                    return (new StringTypeResolver())->resolve((string) $tagValue, $typeContext);
                } catch (UnsupportedException) {
                    // ignore
                }
            }
        }

        return null;
    }
}
