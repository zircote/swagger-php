<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;
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

class TypeInfoTypeResolver extends AbstractTypeResolver
{
    /**
     * @inheritdoc
     */
    protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector, string $sourceClass = OA\Schema::class): void
    {
        $docblockType = $this->getDocblockType($reflector);
        $reflectionType = $this->getReflectionType($reflector);

        // we only consider nullable hints if the type is explicitly set
        if (Undefined::isDefault($schema->nullable)
            && (($docblockType && $docblockType->isNullable())
                || ($reflectionType && $reflectionType->isNullable()))
        ) {
            $schema->nullable = true;
        }

        $docblockType = $docblockType instanceof NullableType ? $docblockType->getWrappedType() : $docblockType;
        $reflectionType = $reflectionType instanceof NullableType ? $reflectionType->getWrappedType() : $reflectionType;

        if (Undefined::isDefault($schema->type, $schema->oneOf, $schema->allOf, $schema->anyOf) && ($docblockType || $reflectionType)) {
            $this->setSchemaType($schema, $docblockType ?? $reflectionType, $analysis, $sourceClass);
        }

        $this->type2ref($schema, $analysis, $sourceClass);

        if ($schema->items instanceof OA\Items) {
            $schema->type = 'array';
        }

        if (!Undefined::isDefault($schema->const) && Undefined::isDefault($schema->type)) {
            if (!$this->mapNativeType($schema, gettype($schema->const))) {
                $schema->type = Undefined::UNDEFINED;
            }
        }

        // final sanity check
        if (!Undefined::isDefault($schema->type) && !$this->mapNativeType($schema, $schema->type)) {
            $schema->type = Undefined::UNDEFINED;
        }
    }

    protected function setSchemaType(OA\Schema $schema, Type $type, Analysis $analysis, string $sourceClass = OA\Schema::class): OA\Schema
    {
        if ($type instanceof CompositeTypeInterface) {
            $types = $type->getTypes();

            $isNonZeroInt = 2 === count($types) && $types[0] instanceof IntRangeType && $types[1] instanceof IntRangeType;

            if ($isNonZeroInt) {
                $schema->type = 'int';
                $schema->not = ['const' => 0];
            } else {
                $allBuiltin = array_reduce($types, static fn ($carry, $t): bool => $carry && $t instanceof BuiltinType, true);

                if ($type instanceof UnionType) {
                    if ($allBuiltin) {
                        $mappableTypes = array_values(array_filter(
                            array_map(static fn (Type $t): string => (string) $t, $types),
                            $this->hasOpenApiType(...),
                        ));
                        $schema->type = [] === $mappableTypes ? Undefined::UNDEFINED : $mappableTypes;
                    } else {
                        $builtinTypes = array_filter($types, static fn (Type $t): bool => $t instanceof BuiltinType);
                        $otherTypes = array_filter($types, static fn (Type $t): bool => !$t instanceof BuiltinType);

                        if ($schema->items instanceof OA\Items) {
                            // nothing more we can do here
                            return $schema;
                        }

                        $schema->type = Undefined::UNDEFINED;
                        $schema->oneOf = [];

                        if ($builtinTypes !== []) {
                            $schema->oneOf[] = $builtinSchema = new OA\Schema([
                                'type' => array_values(array_map(static fn (Type $t): string => (string) $t, $builtinTypes)),
                                '_context' => new Context(['generated' => true], $schema->_context),
                            ]);
                            $this->type2ref($builtinSchema, $analysis);
                            $analysis->addAnnotation($builtinSchema, $builtinSchema->_context);
                        }

                        foreach ($otherTypes as $otherType) {
                            $otherSchema = new OA\Schema([
                                '_context' => new Context(['generated' => true], $schema->_context),
                            ]);
                            $schema->oneOf[] = $this->setSchemaType($otherSchema, $otherType, $analysis);
                            $this->type2ref($otherSchema, $analysis);
                            $analysis->addAnnotation($otherSchema, $otherSchema->_context);
                        }
                    }
                } elseif ($type instanceof IntersectionType) {
                    $schema->type = Undefined::UNDEFINED;
                    $schema->allOf = [];

                    foreach ($types as $intersectionType) {
                        $intersectionSchema = new OA\Schema([
                            '_context' => new Context(['generated' => true], $schema->_context),
                        ]);
                        $schema->allOf[] = $this->setSchemaType($intersectionSchema, $intersectionType, $analysis);
                        $this->type2ref($intersectionSchema, $analysis);
                        $analysis->addAnnotation($intersectionSchema, $intersectionSchema->_context);
                    }
                }
            }
        } else {
            if ($type instanceof BuiltinType) {
                if ($this->hasOpenApiType((string) $type)) {
                    $schema->type = (string) $type;
                }
            } elseif ($type instanceof ObjectType) {
                $schema->type = (string) $type;
            } elseif ($type instanceof IntRangeType) {
                $schema->type = $type->getTypeIdentifier()->value;

                $schema->minimum = $type->getFrom();
                $schema->maximum = $type->getTo();
            } elseif ($type instanceof ExplicitType) {
                $schema->type = $type->getTypeIdentifier()->value;
            } elseif ($type instanceof ArrayShapeType && [] !== $type->getShape()) {
                // array{a: int, b?: string} → object with named properties; array{0: T, 1: U} → positional array
                $this->setSchemaTypeFromArrayShape($schema, $type, $analysis);
            } elseif ($type instanceof CollectionType) {
                if ($type->isList() || $type->getCollectionKeyType() instanceof UnionType) {
                    // list<T>, array<T>, T[] → ordered list
                    $this->setListSchema($schema, $type->getCollectionValueType(), $analysis);
                } else {
                    // explicit key type (e.g. array<string, string>) → map
                    $schema->type = 'object';

                    if (Undefined::isDefault($schema->additionalProperties)) {
                        $schema->additionalProperties = new OA\AdditionalProperties(['_context' => new Context(['generated' => true], $schema->_context)]);
                        $this->setSchemaType($schema->additionalProperties, $type->getCollectionValueType(), $analysis);
                        $this->type2ref($schema->additionalProperties, $analysis);
                        $analysis->addAnnotation($schema->additionalProperties, $schema->additionalProperties->_context);
                    } elseif (Undefined::isDefault($schema->additionalProperties->type, $schema->additionalProperties->oneOf, $schema->additionalProperties->allOf, $schema->additionalProperties->anyOf)) {
                        $this->setSchemaType($schema->additionalProperties, $type->getCollectionValueType(), $analysis);
                        $this->type2ref($schema->additionalProperties, $analysis);
                    }

                    $this->mapNativeType($schema->additionalProperties, $schema->additionalProperties->type);
                }
            }
        }

        return $schema;
    }

    protected function setSchemaTypeFromArrayShape(OA\Schema $schema, ArrayShapeType $type, Analysis $analysis): void
    {
        $shape = $type->getShape();

        // A list-shaped array (array{T, U} or array{0: T, 1: U}) is a positional list, not a keyed object.
        if (array_is_list($shape)) {
            $this->setListSchema($schema, $type->getCollectionValueType(), $analysis);

            return;
        }

        $schema->type = 'object';

        $properties = [];
        $required = [];
        foreach ($shape as $name => $member) {
            $propertyName = (string) $name;
            $property = new OA\Property([
                'property' => $propertyName,
                '_context' => new Context(['generated' => true], $schema->_context),
            ]);
            $this->setSchemaType($property, $member['type'], $analysis);
            $this->type2ref($property, $analysis);
            $this->mapNativeType($property, $property->type);
            $analysis->addAnnotation($property, $property->_context);

            $properties[] = $property;

            if (!($member['optional'] ?? false)) {
                $required[] = $propertyName;
            }
        }

        $schema->properties = $properties;

        if ([] !== $required) {
            $schema->required = $required;
        }

        /*
         * An unsealed shape permits extra entries.
         * For example array{a: int, ...} or array{a: int, ...<T>}.
         * The schema therefore allows additional properties.
         *
         * The rest value type (...<T>) is left open, not emitted.
         * Symfony's type-info resolves it inconsistently across versions.
         * The same ...<string> comes back as string, int|string, or unresolved.
         * Emitting it would be unstable and sometimes invalid.
         *
         * A sealed shape has a null extra value type.
         * A non-null one means the shape is open.
         */
        if ($type->getExtraValueType() instanceof Type) {
            $schema->additionalProperties = new OA\AdditionalProperties(['_context' => new Context(['generated' => true], $schema->_context)]);
            $analysis->addAnnotation($schema->additionalProperties, $schema->additionalProperties->_context);
        }
    }

    /**
     * Emits an ordered-list schema (type: array) whose items resolve from the given value type.
     *
     * Used for both collection lists (list<T>, array<T>, T[]) and positional array shapes (array{0: T, 1: U}).
     */
    protected function setListSchema(OA\Schema $schema, Type $valueType, Analysis $analysis): void
    {
        $schema->type = 'array';

        if (Undefined::isDefault($schema->items)) {
            $schema->items = new OA\Items(['_context' => new Context(['generated' => true], $schema->_context)]);
            $this->setSchemaType($schema->items, $valueType, $analysis);
            $this->type2ref($schema->items, $analysis);
            $analysis->addAnnotation($schema->items, $schema->items->_context);
        } elseif (Undefined::isDefault($schema->items->type, $schema->items->oneOf, $schema->items->allOf, $schema->items->anyOf)) {
            $this->setSchemaType($schema->items, $valueType, $analysis);
            $this->type2ref($schema->items, $analysis);
        }

        $this->mapNativeType($schema->items, $schema->items->type);
    }

    protected function hasOpenApiType(string $native): bool
    {
        return $this->typeMapper->hasOpenApiType($native);
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
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
            // ignore
        }

        return null;
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
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

        $lexer = new Lexer(new ParserConfig([]));
        $phpDocParser = new PhpDocParser(
            $config = new ParserConfig([]),
            new TypeParser($config, $constExprParser = new ConstExprParser($config)),
            $constExprParser,
        );

        $tokens = new TokenIterator($lexer->tokenize($docComment));
        $docNode = $phpDocParser->parse($tokens);

        foreach ($docNode->getTagsByName($tagName) as $tag) {
            $tagValue = $tag->value;

            if (
                $tagValue instanceof VarTagValueNode
                || ($tagValue instanceof ParamTagValueNode && $tagName && '$' . $reflector->getName() === $tagValue->parameterName)
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
