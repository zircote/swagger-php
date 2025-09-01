<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Context;
use OpenApi\TypeResolverInterface;

class LegacyTypeResolver implements TypeResolverInterface
{
    protected ?Context $context;

    public function __construct(?Context $context = null)
    {
        $this->context = $context;
    }

    protected function normaliseTypeResult(?string $explicitType = null, array $types = [], ?string $name = null, ?bool $nullable = null, ?bool $isArray = null): \stdClass
    {
        if ($this->context) {
            foreach ($types as $ii => $type) {
                if (!array_key_exists(strtolower($type), TypeResolverInterface::NATIVE_TYPE_MAP) && !class_exists($type)) {
                    if (($resolved = $this->context->fullyQualifiedName($type)) && class_exists($resolved)) {
                        $types[$ii] = ltrim($resolved, '\\');
                    }
                }
            }
        }

        $explicitType = $explicitType ?: ($types ? $types[0] : null);

        return (object) [
            'explicitType' => $explicitType,
            'types' => $types,
            'name' => $name,
            'nullable' => $explicitType ? $nullable : true,
            'isArray' => $isArray,
        ];
    }

    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass
    {
        $rtype = $reflector instanceof \ReflectionClass
            ? $reflector->getName()
            : (
                $reflector instanceof \ReflectionMethod
                ? $reflector->getReturnType()
                : ($reflector->getType())
            );

        $isArray = false;

        $types = [];
        if ($rtype instanceof \ReflectionUnionType) {
            foreach ($rtype->getTypes() as $utype) {
                // more nesting is not supported
                if ($utype instanceof \ReflectionNamedType) {
                    $types[] = $utype->getName();
                }
            }
        } elseif ($rtype instanceof \ReflectionNamedType) {
            $types[] = $rtype->getName();
        }

        if (1 === count($types) && 'array' === $types[0]) {
            $types = ['mixed'];
            $isArray = true;
        }

        $name = $reflector->getName();
        $nullable = is_object($rtype) ? $rtype->allowsNull() : true;

        return $this->normaliseTypeResult(null, array_reverse($types), $name, $nullable, $isArray);
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

        // cheat
        $name = $reflector->getName();

        if (!$docComment) {
            return $this->normaliseTypeResult(null, [], $name);
        }

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

        if (!$tagName) {
            return $this->normaliseTypeResult(null, [], $name);
        }

        $pattern = "/$tagName\s+(?<type>[^\s]+)([ \t])?/im";
        if ('@param' === $tagName) {
            // need to match on $name too
            $pattern = "/$tagName\s+(?<type>[^\s]+)([ \t])?\\$$name/im";
        }

        $docComment = str_replace("\r\n", "\n", $docComment);
        $docComment = preg_replace('/\*\/[ \t]*$/', '', $docComment); // strip '*/'
        preg_match($pattern, $docComment, $matches);

        $explicitType = null;
        $type = $matches['type'] ?? '';
        $nullable = in_array('null', explode('|', strtolower($type))) || str_contains($type, '?');
        $isArray = str_contains($type, '[]') || str_contains($type, 'array');
        $type = str_replace(['|null', 'null|', '?', 'null', '[]'], '', $type);

        // typed array
        $result = preg_match('/([^<]+)<([^>]+)>/', $type, $matches);
        if ($result) {
            $type = $isArray ? $matches[2] : $matches[1];
        }

        // partial array shape
        $result = preg_match('/([^{]+){([^}]+)}/', $type, $matches);
        if ($result) {
            $shapeTypes = [];
            foreach (explode(',', $matches[2]) as $shape) {
                $token = explode(':', $shape);
                if (2 === count($token)) {
                    $shapeTypes[trim($token[0])] = trim($token[1]);
                }
            }
            $type = implode('|', $shapeTypes);
        }

        // special types
        switch ($type) {
            case 'positive-int':
            case 'negative-int':
            case 'non-positive-int':
            case 'non-negative-int':
            case 'non-zero-int':
                $explicitType = $type;
                $type = 'int';
                break;
        }

        $type = ltrim($type, '\\');
        $types = explode('|', $type);

        return $this->normaliseTypeResult($explicitType, $types, $name, $nullable, $isArray);
    }
}
