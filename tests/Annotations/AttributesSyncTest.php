<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class AttributesSyncTest extends OpenApiTestCase
{
    public static $SCHEMA_EXCLUSIONS = ['const', 'multipleOf', 'not', 'additionalItems', 'contains', 'dependencies', 'propertyNames'];

    public static $PATHITEM_EXCLUSIONS = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];

    public static $PARAMETER_EXCLUSIONS = ['matrix', 'label', 'form', 'simple', 'deepObject'];

    public function testCounts(): void
    {
        $this->assertSameSize($this->allAnnotationClasses(), $this->allAttributeClasses());
    }

    #[DataProvider('allAnnotationClasses')]
    public function testParameterCompleteness(string $annotation): void
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

                    if ($annotationType !== $attributeType) {
                        $typeMismatch[$propertyName] = [$annotationRC->getName(), $annotationType, $attributeType];
                    }

                    $found = true;
                    break;
                }
            }
            // oh, well...
            if ($attributeRC->isSubclassOf(OA\Parameter::class)) {
                // not relevant
                unset($typeMismatch['in']);
                // uses inheritdoc
                unset($typeMismatch['required']);
            }
            if (!$found) {
                // Schema inheritance exclusions...
                if ($attributeRC->isSubclassOf(OA\Operation::class) && $propertyName == 'method') {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Attachable::class) && $propertyName == 'x') {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\AdditionalProperties::class) && in_array($propertyName, ['additionalProperties', 'examples', 'contentEncoding', 'contentMediaType'])) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Items::class) && in_array($propertyName, ['examples', 'contentEncoding', 'contentMediaType'])) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Property::class) && in_array($propertyName, ['examples', 'contentEncoding', 'contentMediaType'])) {
                    continue;
                }

                if (in_array($propertyName, self::$SCHEMA_EXCLUSIONS)) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\PathItem::class) && in_array($propertyName, self::$PATHITEM_EXCLUSIONS)) {
                    continue;
                }
                if ($attributeRC->isSubclassOf(OA\Parameter::class) && in_array($propertyName, self::$PARAMETER_EXCLUSIONS)) {
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

    protected function prepDocComment(string $docComment): array
    {
        if (!$docComment) {
            return [];
        }

        $lines = preg_split('/(\n|\r\n)/', $docComment);
        $lines[0] = preg_replace('/[ \t]*\\/\*\*/', '', $lines[0]); // strip '/**'
        $i = count($lines) - 1;
        $lines[$i] = preg_replace('/\*\/[ \t]*$/', '', (string) $lines[$i]); // strip '*/'

        foreach ($lines as $ii => $line) {
            $lines[$ii] = ltrim((string) $line, "\t *");
        }

        return $lines;
    }

    protected function propertyType(\ReflectionProperty $property): string
    {
        $var = 'mixed';
        foreach ($this->prepDocComment($property->getDocComment()) as $line) {
            if (str_starts_with((string) $line, '@var ')) {
                $var = trim(substr((string) $line, 5));
            }
        }

        if ($var) {
            $var = str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\'], '', $var);
            if (!str_contains($var, '<')) {
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
            if (str_starts_with((string) $line, '@')) {
                if (str_starts_with((string) $line, '@param ')) {
                    $line = preg_replace('/ +/', ' ', (string) $line);
                    $token = explode(' ', trim(substr((string) $line, 7)));
                    if (2 === count($token)) {
                        [$type, $name] = $token;
                        if (str_replace('$', '', $name) === $parameterName) {
                            $var = str_replace(['|null', 'null|'], '', $type);
                        }
                    }
                }
            }
        }

        if ($var) {
            $var = str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\', 'OA'], '', $var);
            if (!str_contains($var, '<')) {
                $var = explode('|', $var);
                sort($var);
                $var = implode('|', $var);
            }
        }

        return $var;
    }

    #[DataProvider('allAttributeClasses')]
    public function testPropertyCompleteness(string $attribute): void
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
