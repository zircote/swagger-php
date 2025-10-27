<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\TypeResolverInterface;
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
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\ReflectionTypeResolver;

class TypeInfoTypeResolver extends AbstractTypeResolver
{
    /** @inheritdoc */
    protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector): void
    {
        $context = $schema->_context;
        $docblockDetails = $this->getDocblockTypeDetails($reflector);
        $reflectionTypeDetails = $this->getReflectionTypeDetails($reflector);

        $type2ref = function (OA\Schema $schema) use ($analysis): void {
            if (!Generator::isDefault($schema->type)) {
                if ($typeSchema = $analysis->getSchemaForSource($schema->type)) {
                    $schema->type = Generator::UNDEFINED;
                    $schema->ref = OA\Components::ref($typeSchema);
                }
            }
        };

        // we only consider nullable hints if the type is explicitly set
        if (Generator::isDefault($schema->nullable)
            && (($docblockDetails->types && $docblockDetails->nullable)
                || ($reflectionTypeDetails->types && $reflectionTypeDetails->nullable))
        ) {
            $schema->nullable = true;
        }

        if (Generator::isDefault($schema->type) && ($docblockDetails->explicitType || $reflectionTypeDetails->explicitType)) {
            $details = $docblockDetails->types && $docblockDetails->isArray
                // for arrays, we prefer the docblock type
                ? $docblockDetails
                // otherwise, use the reflection type if possible
                : ($reflectionTypeDetails->types ? $reflectionTypeDetails : $docblockDetails);

            // for now
            if (1 === count($details->types)) {
                $schema->type = $details->types[0];
            }

            if ('int' === $schema->type && is_array($details->explicitDetails)) {
                if (array_key_exists('min', $details->explicitDetails)) {
                    $schema->minimum = $details->explicitDetails['min'];
                    $schema->maximum = $details->explicitDetails['max'];
                } elseif ('non-zero-int' === $details->explicitType) {
                    $schema->not = $schema->_context->isVersion('3.1.x')
                        ? ['const' => 0]
                        : ['enum' => [0]];
                }
            }
        }

        if ($docblockDetails->isArray || $reflectionTypeDetails->isArray) {
            if (Generator::isDefault($schema->items)) {
                $schema->items = new OA\Items(
                    [
                        'type' => $schema->type,
                        '_context' => new Context(['generated' => true], $context),
                    ]
                );

                $type2ref($schema->items);

                $analysis->addAnnotation($schema->items, $schema->items->_context);

                if (!Generator::isDefault($schema->ref)) {
                    $schema->items->ref = $schema->ref;
                    $schema->ref = Generator::UNDEFINED;
                }
            }

            $schema->type = 'array';
        } else {
            $type2ref($schema);
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
    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    protected function normaliseTypeResult(\Reflector $reflector, ?Type $resolved): \stdClass
    {
        $details = (object) [
            'explicitType' => null,
            'explicitDetails' => null,
            'types' => [],
            'name' => $reflector->getName(),
            'nullable' => false,
            'isArray' => false,
        ];

        if (!$resolved) {
            $details->nullable = true;

            return $details;
        }

        $fromType = function (Type $type, $details): void {
            if ($type instanceof BuiltinType || $type instanceof ObjectType) {
                $details->types[] = (string) $type;
            } elseif ($type instanceof CollectionType) {
                $details->isArray = true;
                $details->types[] = (string) $type->getCollectionValueType();
            } elseif ($type instanceof IntRangeType) {
                // use just `int` for custom `int<..>`
                $details->explicitType = str_contains($type->getExplicitType(), '<')
                    ? $type->getTypeIdentifier()->value
                    : $type->getExplicitType();
                $details->explicitDetails = [
                    'min' => $type->getFrom(),
                    'max' => $type->getTo(),
                ];
                $details->types[] = $type->getTypeIdentifier()->value;
            } elseif ($type instanceof ExplicitType) {
                $details->explicitType = $type->getExplicitType();
                $details->types[] = $type->getTypeIdentifier()->value;
            }
        };

        $handleNonZeroInt = function (array $utypes) use ($details): void {
            // non-zero-int
            if (2 === count($utypes) && $utypes[0] instanceof IntRangeType && $utypes[1] instanceof IntRangeType) {
                $details->explicitType = 'non-zero-int';
                $details->explicitDetails = [['min' => \PHP_INT_MIN, 'max' => -1], ['min' => 1, 'max' => \PHP_INT_MAX]];
                $details->types = array_unique($details->types);
            }
        };

        if ($resolved instanceof NullableType) {
            $details->nullable = true;
            $fromType($wrapped = $resolved->getWrappedType(), $details);
            if ($wrapped instanceof UnionType) {
                foreach (($utypes = $wrapped->getTypes()) as $utype) {
                    $fromType($utype, $details);
                }

                $handleNonZeroInt($utypes);
            }
        } elseif ($resolved instanceof UnionType) {
            foreach (($utypes = $resolved->getTypes()) as $utype) {
                $fromType($utype, $details);
            }

            $handleNonZeroInt($utypes);
        } else {
            $fromType($resolved, $details);
        }

        if (in_array('null', $details->types)) {
            $details->nullable = true;
            // @phpstan-ignore notIdentical.alwaysTrue
            $details->types = array_filter($details->types, fn ($t): bool => 'null' !== $t);
        }

        $details->explicitType ??= $details->types[0] ?? null;

        return $details;
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass
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
            $resolved = (new ReflectionTypeResolver())->resolve($subject, $typeContext);
        } catch (UnsupportedException $unsupportedException) {
            $resolved = null;
        }

        return $this->normaliseTypeResult($reflector, $resolved);
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getDocblockTypeDetails(\Reflector $reflector): \stdClass
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
            return $this->normaliseTypeResult($reflector, null);
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
                || $tagValue instanceof ParamTagValueNode && $tagName && '$' . $reflector->getName() === $tagValue->parameterName
                || $tagValue instanceof ReturnTagValueNode
            ) {
                try {
                    $resolved = (new StringTypeResolver())->resolve((string) $tagValue, $typeContext);

                    return $this->normaliseTypeResult($reflector, $resolved);
                } catch (UnsupportedException $e) {
                    // ignore
                }
            }
        }

        return $this->normaliseTypeResult($reflector, null);
    }
}
