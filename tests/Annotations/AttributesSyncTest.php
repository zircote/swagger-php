<?php declare(strict_types=1);

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

/**
 * @requires PHP 8.1
 */
class AttributesSyncTest extends OpenApiTestCase
{
    public static $SCHEMA_EXCLUSIONS = ['const', 'maxProperties', 'minProperties', 'multipleOf', 'not', 'additionalItems', 'contains', 'patternProperties', 'dependencies', 'propertyNames'];
    public static $PATHITEM_EXCLUSIONS = ['ref', 'get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];
    public static $PARAMETER_EXCLUSIONS = ['content', 'matrix', 'label', 'form', 'simple', 'deepObject'];

    public function testCounts()
    {
        $this->assertSameSize($this->allAnnotationClasses(), $this->allAttributeClasses());
    }

    /**
     * @dataProvider allAnnotationClasses
     */
    public function testParameterCompleteness($annotation): void
    {
        $annotationRC = new \ReflectionClass($annotation);
        $attributeRC = new \ReflectionClass('OpenApi\\Attributes\\' . $annotationRC->getShortName());
        $attributeCtor = $attributeRC->getMethod('__construct');
        $attributeParameters = $attributeCtor->getParameters();

        $missing = [];
        $typeMismatch = [];
        foreach ($annotationRC->getProperties() as $property) {
            $propertyName = $property->getName();
            if (in_array($propertyName, $annotation::$_blacklist) || $propertyName[0] == '_') {
                continue;
            }

            $found = false;
            foreach ($attributeParameters as $attributeParameter) {
                if ($attributeParameter->getName() == $propertyName) {
                    $annotationType = $this->propertyType($property);
                    $attributeType = $this->parameterType($propertyName, $attributeParameter);

                    if ($annotationType != $attributeType) {
                        $typeMismatch[$propertyName] = [$annotationType, $attributeType];
                    }

                    $found = true;
                    break;
                }
            }
            // oh, well...
            if ($attributeRC->isSubclassOf(OA\PathParameter::class)) {
                // not relevant
                unset($typeMismatch['in']);
                // uses inheritdoc
                unset($typeMismatch['required']);
            }
            if (!$found) {
                // exclusions...
                if ($attributeRC->isSubclassOf(OA\Operation::class) && 'method' == $propertyName) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Attachable::class) && 'x' == $propertyName) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\AdditionalProperties::class) && 'additionalProperties' == $propertyName) {
                    continue;
                }
                if (in_array($propertyName, static::$SCHEMA_EXCLUSIONS)) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\PathItem::class) && in_array($propertyName, static::$PATHITEM_EXCLUSIONS)) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Parameter::class) && in_array($propertyName, static::$PARAMETER_EXCLUSIONS)) {
                    continue;
                }
                $missing[] = $propertyName;
            }
        }

        if ($missing) {
            $this->fail('Missing parameters: ' . implode(', ', $missing));
        }

        if ($typeMismatch) {
            var_dump($typeMismatch);
            $this->fail('Type mismatch: ' . count($typeMismatch));
        }
    }

    protected function prepDocComment($docComment): array
    {
        if (!$docComment) {
            return [];
        }

        $lines = preg_split('/(\n|\r\n)/', $docComment);
        $lines[0] = preg_replace('/[ \t]*\\/\*\*/', '', $lines[0]); // strip '/**'
        $i = count($lines) - 1;
        $lines[$i] = preg_replace('/\*\/[ \t]*$/', '', $lines[$i]); // strip '*/'

        foreach ($lines as $ii => $line) {
            $lines[$ii] = ltrim($line, "\t *");
        }

        return $lines;
    }

    protected function propertyType(\ReflectionProperty $property): ?string
    {
        $var = null;
        foreach ($this->prepDocComment($property->getDocComment()) as $line) {
            if (substr($line, 0, 1) === '@') {
                if (substr($line, 0, 5) === '@var ') {
                    $var = trim(substr($line, 5));
                }
            }
        }

        if ($var) {
            $var = str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\'], '', $var);
            if (false === strpos($var, '<')) {
                $var = explode('|', $var);
                sort($var);
                $var = implode('|', $var);
            }
        }

        return $var;
    }

    protected function parameterType(string $parameterName, \ReflectionParameter $parameter): ?string
    {
        $var = null;
        if ($type = $parameter->getType()) {
            if ($type instanceof \ReflectionUnionType) {
                $var = [];
                foreach ($type->getTypes() as $unionType) {
                    if ('null' != $unionType->getName()) {
                        // null means default for most parameters
                        $var[] = $unionType->getName();
                    }
                }
                sort($var);
                $var = implode('|', $var);
            } else {
                $var = $type->getName();
            }
        }

        foreach ($this->prepDocComment($parameter->getDeclaringFunction()->getDocComment()) as $line) {
            if (substr($line, 0, 1) === '@') {
                if (substr($line, 0, 7) === '@param ') {
                    $line = preg_replace('/ +/', ' ', $line);
                    $token = explode(' ', trim(substr($line, 7)));
                    if (2 == count($token)) {
                        [$type, $name] = $token;
                        if (str_replace('$', '', $name) == $parameterName) {
                            $var = str_replace(['|null', 'null|'], '', $type);
                        }
                    }
                }
            }
        }

        if ($var) {
            $var = str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\'], '', $var);
            if (false === strpos($var, '<')) {
                $var = explode('|', $var);
                sort($var);
                $var = implode('|', $var);
            }
        }

        return $var;
    }

    /**
     * @dataProvider allAttributeClasses
     */
    public function testPropertyCompleteness($attribute)
    {
        $attributeRC = new \ReflectionClass($attribute);
        $annotationRC = new \ReflectionClass('OpenApi\\Annotations\\' . $attributeRC->getShortName());
        $attributeCtor = $attributeRC->getMethod('__construct');

        $stale = [];
        foreach ($attributeCtor->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            if (!$annotationRC->hasProperty($parameterName)) {
                // exclusions...
                if ($attributeRC->isSubclassOf(OA\Attachable::class) && 'properties' == $parameterName) {
                    continue;
                }
                $stale[] = $parameterName;
            }
        }

        if ($stale) {
            $this->fail('Stale parameters: ' . implode(', ', $stale));
        }
    }
}
