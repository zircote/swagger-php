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
    /** @var list<string> */
    public static array $SCHEMA_EXCLUSIONS = ['multipleOf', 'dependencies', 'propertyNames'];

    /** @var list<string> */
    public static array $PATHITEM_EXCLUSIONS = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];

    /** @var list<string> */
    public static array $PARAMETER_EXCLUSIONS = ['matrix', 'label', 'form', 'simple'];

    public function testCounts(): void
    {
        $this->assertSameSize($this->allAnnotationClasses(), $this->allAttributeClasses());
    }

    /**
     * @param class-string $annotation
     */
    #[DataProvider('allAnnotationClasses')]
    public function testParameterCompleteness(string $annotation): void
    {
        $annotationRC = new \ReflectionClass($annotation);
        /** @var class-string $attributeName */
        $attributeName = 'OpenApi\\Attributes\\' . $annotationRC->getShortName();
        $attributeRC = new \ReflectionClass($attributeName);
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

        if ($missing !== []) {
            $this->fail('Missing parameters: ' . implode(', ', $missing));
        }

        if ($typeMismatch !== []) {
            var_dump($typeMismatch);
            $this->fail('Type mismatch: ' . count($typeMismatch));
        }
    }

    /**
     * @param class-string $attribute
     */
    #[DataProvider('allAttributeClasses')]
    public function testPropertyCompleteness(string $attribute): void
    {
        $attributeRC = new \ReflectionClass($attribute);
        /** @var class-string $annotationName */
        $annotationName = 'OpenApi\\Annotations\\' . $attributeRC->getShortName();
        $annotationRC = new \ReflectionClass($annotationName);
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

        if ($stale !== []) {
            $this->fail('Stale parameters: ' . implode(', ', $stale));
        }
    }

    /**
     * @return array<mixed>
     */
    /**
     * @param string|false $docComment as getDocComment() returns it
     *
     * @return list<string>
     */
    protected function prepDocComment(string|false $docComment): array
    {
        if (!$docComment) {
            return [];
        }

        $lines = preg_split('/(\n|\r\n)/', $docComment) ?: [];
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
                    if (!$unionType instanceof \ReflectionNamedType) {
                        continue;
                    }
                    if ('null' !== $unionType->getName()) {
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

        if (!is_string($var) || '' === $var) {
            return null;
        }

        $var = str_replace(['OpenApi\\Annotations\\', 'OpenApi\\Attributes\\', 'OA'], '', $var);
        if (!str_contains($var, '<')) {
            $parts = explode('|', $var);
            sort($parts);
            $var = implode('|', $parts);
        }

        return $var;
    }
}
