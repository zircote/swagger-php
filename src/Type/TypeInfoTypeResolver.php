<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
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
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;

class TypeInfoTypeResolver extends AbstractTypeResolver
{
    /** @inheritdoc */
    protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector, string $sourceClass = OA\Schema::class): void
    {
        $docblockType = $this->getDocblockType($reflector);
        $reflectionType = $this->getReflectionType($reflector);

        // we only consider nullable hints if the type is explicitly set
        if (Generator::isDefault($schema->nullable)
            && (($docblockType && $docblockType->isNullable())
                || ($reflectionType && $reflectionType->isNullable()))
        ) {
            $schema->nullable = true;
        }

        $docblockType = $docblockType instanceof NullableType ? $docblockType->getWrappedType() : $docblockType;
        $reflectionType = $reflectionType instanceof NullableType ? $reflectionType->getWrappedType() : $reflectionType;

        if (Generator::isDefault($schema->type, $schema->oneOf, $schema->allOf, $schema->anyOf) && ($docblockType || $reflectionType)) {
            $type = $docblockType ?? $reflectionType;

            $isNonZeroInt = false;
            if ($type instanceof CompositeTypeInterface && 2 === count($type->getTypes())) {
                $types = $type->getTypes();
                $isNonZeroInt = $types[0] instanceof IntRangeType && $types[1] instanceof IntRangeType;
            }

            if (!$type instanceof CompositeTypeInterface || $isNonZeroInt) {
                if ($isNonZeroInt) {
                    $schema->type = 'int';
                    $schema->not = $schema->_context->isVersion('3.0.x')
                        ? ['enum' => [0]]
                        : ['const' => 0];
                } elseif ($type instanceof BuiltinType || $type instanceof ObjectType) {
                    $schema->type = (string) $type;
                } elseif ($type instanceof IntRangeType) {
                    $schema->type = $type->getTypeIdentifier()->value;

                    $schema->minimum = $type->getFrom();
                    $schema->maximum = $type->getTo();

                } elseif ($type instanceof ExplicitType) {
                    $schema->type = $type->getTypeIdentifier()->value;
                } elseif ($type instanceof CollectionType) {
                    $schema->type = (string) $type->getCollectionValueType();
                }
            }
        }

        if ($docblockType instanceof CollectionType || $reflectionType instanceof CollectionType) {
            $this->augmentItems($schema, $analysis);
        }

        $this->type2ref($schema, $analysis, $sourceClass);

        if ($schema->items instanceof OA\Items) {
            $schema->type = 'array';
        }

        if (!Generator::isDefault($schema->const) && Generator::isDefault($schema->type)) {
            if (!$this->mapNativeType($schema, gettype($schema->const))) {
                $schema->type = Generator::UNDEFINED;
            }
        }

        // final sanity check
        if (!Generator::isDefault($schema->type) && !$this->mapNativeType($schema, $schema->type)) {
            $schema->type = Generator::UNDEFINED;
        }
    }

<<<<<<< HEAD
    /**
=======
    protected function setSchemaType(OA\Schema $schema, Type $type, Analysis $analysis, string $sourceClass = OA\Schema::class): OA\Schema
    {
        if ($type instanceof CompositeTypeInterface) {
            $types = $type->getTypes();

            $isNonZeroInt = 2 === count($types) && $types[0] instanceof IntRangeType && $types[1] instanceof IntRangeType;

            if ($isNonZeroInt) {
                $schema->type = 'int';
                $schema->not = $schema->_context->isVersion('3.0.x')
                    ? ['enum' => [0]]
                    : ['const' => 0];
            } else {
                $allBuiltin = array_reduce($types, static fn ($carry, $t): bool => $carry && $t instanceof BuiltinType, true);

                if ($type instanceof UnionType) {
                    if ($allBuiltin) {
                        $schema->type = array_map(static fn (Type $t): string => (string) $t, $types);
                    } else {
                        $builtinTypes = array_filter($types, static fn (Type $t): bool => $t instanceof BuiltinType);
                        $otherTypes = array_filter($types, static fn (Type $t): bool => !$t instanceof BuiltinType);

                        if ($schema->items instanceof OA\Items) {
                            // nothing more we can do here
                            return $schema;
                        }

                        $schema->type = Generator::UNDEFINED;
                        $schema->oneOf = [];

                        if ($builtinTypes) {
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
                    $schema->type = Generator::UNDEFINED;
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
            if ($type instanceof BuiltinType || $type instanceof ObjectType) {
                $schema->type = (string) $type;
            } elseif ($type instanceof IntRangeType) {
                $schema->type = $type->getTypeIdentifier()->value;

                $schema->minimum = $type->getFrom();
                $schema->maximum = $type->getTo();
            } elseif ($type instanceof ExplicitType) {
                $schema->type = $type->getTypeIdentifier()->value;
            } elseif ($type instanceof CollectionType) {
                $schema->type = 'array';

                if (Generator::isDefault($schema->items)) {
                    $schema->items = new OA\Items(['_context' => new Context(['generated' => true], $schema->_context)]);
                    $this->setSchemaType($schema->items, $type->getCollectionValueType(), $analysis);
                    $this->type2ref($schema->items, $analysis);
                    $analysis->addAnnotation($schema->items, $schema->items->_context);
                } elseif (Generator::isDefault($schema->items->type, $schema->items->oneOf, $schema->items->allOf, $schema->items->anyOf)) {
                    $this->setSchemaType($schema->items, $type->getCollectionValueType(), $analysis);
                    $this->type2ref($schema->items, $analysis);
                }

                $this->mapNativeType($schema->items, $schema->items->type);
            }
        }

        return $schema;
    }

    /**645 1050272  02 1268 0026220 00
>>>>>>> 0e20d17 (Skip type resolving on `Schema` with `items` set (#1916) the )
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
        } catch (UnsupportedException $unsupportedException) {
            // ignore
        }

        return null;
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getDocblockType(\Reflector $reflector): ?Type
    {
        switch (true) {
            case $reflector instanceof \ReflectionProperty:
                $docComment = (method_exists($reflector, 'isPromoted') && $reflector->isPromoted())
                && $reflector->getDeclaringClass() && $reflector->getDeclaringClass()->getConstructor()
                    ? $reflector->getDeclaringClass()->getConstructor()->getDocComment()
                    : $reflector->getDocComment();
                break;
            case $reflector instanceof \ReflectionParameter:
                $docComment = $reflector->getDeclaringFunction()->getDocComment();
                break;
            case $reflector instanceof \ReflectionFunctionAbstract:
                $docComment = $reflector->getDocComment();
                break;
            default:
                $docComment = null;
        }

        if (!$docComment) {
            return null;
        }

        $typeContext = (new TypeContextFactory())->createFromReflection($reflector);

        switch (true) {
            case $reflector instanceof \ReflectionProperty:
                $tagName = (method_exists($reflector, 'isPromoted') && $reflector->isPromoted())
                    ? '@param'
                    : '@var';
                break;
            case $reflector instanceof \ReflectionParameter:
                $tagName = '@param';
                break;
            case $reflector instanceof \ReflectionFunctionAbstract:
                $tagName = '@return';
                break;
            default:
                $tagName = null;
        }

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
                } catch (UnsupportedException $e) {
                    // ignore
                }
            }
        }

        return null;
    }
}
