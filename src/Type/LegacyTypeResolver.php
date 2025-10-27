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

class LegacyTypeResolver extends AbstractTypeResolver
{
    protected ?Context $context;

    public function __construct(?Context $context = null)
    {
        $this->context = $context;
    }

    public function setContext(Context $context): LegacyTypeResolver
    {
        $this->context = $context;

        return $this;
    }

    /** @inheritdoc */
    protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector): void
    {
        $this->context = $context = $schema->_context;
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

    protected function normaliseTypeResult(?string $explicitType = null, ?array $explicitDetails = null, array $types = [], ?string $name = null, ?bool $nullable = null, ?bool $isArray = null): \stdClass
    {
        $types = array_filter($types, fn (string $t): bool => !in_array($t, ['null', '']));

        if ($this->context) {
            foreach ($types as $ii => $type) {
                if (!array_key_exists(strtolower($type), TypeResolverInterface::NATIVE_TYPE_MAP) && !class_exists($type)) {
                    if (($resolved = $this->context->fullyQualifiedName($type)) && class_exists($resolved)) {
                        $types[$ii] = ltrim($resolved, '\\');
                    } else {
                        // invalid type
                        unset($types[$ii]);
                    }
                }
            }
            // ensure we reset numeric keys
            $types = array_values($types);
        }

        $explicitType = $explicitType ?: ($types ? $types[0] : null);

        return (object) [
            'explicitType' => $explicitType,
            'explicitDetails' => $explicitDetails,
            'types' => $types,
            'name' => $name,
            'nullable' => $explicitType ? $nullable : true,
            'isArray' => $isArray,
        ];
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass
    {
        $rtype = $reflector instanceof \ReflectionClass
            ? $reflector->getName()
            : (
                $reflector instanceof \ReflectionMethod
                ? $reflector->getReturnType()
                : (method_exists($reflector, 'getType') ? $reflector->getType() : null)
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

        $nullable = (is_object($rtype) ? $rtype->allowsNull() : true) || in_array('null', $types);

        return $this->normaliseTypeResult(null, null, array_reverse($types), $name, $nullable, $isArray);
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

        // cheat
        $name = $reflector->getName();

        if (!$docComment) {
            return $this->normaliseTypeResult(null, null, [], $name);
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
            return $this->normaliseTypeResult(null, null, [], $name);
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
        $explicitDetails = null;
        $type = $matches['type'] ?? '';
        $nullable = in_array('null', explode('|', strtolower($type))) || str_contains($type, '?');
        $isArray = str_contains($type, '[]') || str_contains($type, 'array');
        $type = str_replace(['|null', 'null|', '?', 'null', '[]'], '', $type);

        // typed array
        $result = preg_match('/([^<]+)<([^>]+)>/', $type, $matches);
        if ($result) {
            $type = $isArray ? $matches[2] : $matches[1];
            if ('int' === $type) {
                $minMax = array_map(fn (string $s): string => trim($s), explode(',', $matches[2]));
                if (2 === count($minMax)) {
                    $explicitDetails = [
                        'min' => (int) ('min' === $minMax[0] ? \PHP_INT_MIN : $minMax[0]),
                        'max' => (int) ('max' === $minMax[1] ? \PHP_INT_MAX : $minMax[1]),
                    ];
                }
            }
        }

        // array shape
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
                $explicitType = $type;
                $explicitDetails = ['min' => 1, 'max' => \PHP_INT_MAX];
                $type = 'int';
                break;
            case 'negative-int':
                $explicitType = $type;
                $explicitDetails = ['min' => \PHP_INT_MIN, 'max' => -1];
                $type = 'int';
                break;
            case 'non-positive-int':
                $explicitType = $type;
                $explicitDetails = ['min' => \PHP_INT_MIN, 'max' => 0];
                $type = 'int';
                break;
            case 'non-negative-int':
                $explicitType = $type;
                $explicitDetails = ['min' => 0, 'max' => \PHP_INT_MAX];
                $type = 'int';
                break;
            case 'non-zero-int':
                $explicitType = $type;
                $explicitDetails = [['min' => \PHP_INT_MIN, 'max' => -1], ['min' => 1, 'max' => \PHP_INT_MAX]];
                $type = 'int';
                break;
        }

        $type = ltrim($type, '\\');
        $types = explode('|', $type);

        return $this->normaliseTypeResult($explicitType, $explicitDetails, $types, $name, $nullable, $isArray);
    }
}
