<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

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

class TypeInfoTypeResolver implements TypeResolverInterface
{
    protected function normaliseTypeResult(\Reflector $reflector, ?Type $resolved): \stdClass
    {
        $details = (object) [
            'explicitType' => null,
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
                $details->types[] = $type->getCollectionValueType();
            } elseif ($type instanceof IntRangeType) {
                // use just `int` for custom `int<..>`
                $details->explicitType = str_contains($type->getExplicitType(), '<')
                    ? $type->getTypeIdentifier()->value
                    : $type->getExplicitType();
                $details->types[] = $type->getTypeIdentifier()->value;
            } elseif ($type instanceof ExplicitType) {
                $details->explicitType = $type->getExplicitType();
                $details->types[] = $type->getTypeIdentifier()->value;
            }
        };

        if ($resolved instanceof NullableType) {
            $details->nullable = true;
            $fromType($resolved->getWrappedType(), $details);
        } elseif ($resolved instanceof UnionType) {
            foreach ($resolved->getTypes() as $utype) {
                $fromType($utype, $details);
            }
        } else {
            $fromType($resolved, $details);
        }

        $details->explicitType = $details->explicitType ?: ($details->types ? $details->types[0] : null);

        return $details;
    }

    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass
    {
        $subject = $reflector instanceof \ReflectionClass
            ? $reflector->getName()
            : (
                $reflector instanceof \ReflectionMethod
                ? $reflector->getReturnType()
                : ($reflector->getType())
            );
        try {
            $typeContext = (new TypeContextFactory())->createFromReflection($reflector);
            $resolved = (new ReflectionTypeResolver())->resolve($subject, $typeContext);
        } catch (UnsupportedException $unsupportedException) {
            $resolved = null;
        }

        return $this->normaliseTypeResult($reflector, $resolved);
    }

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
