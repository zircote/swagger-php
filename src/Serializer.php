<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations as OA;
use Symfony\Component\Yaml\Yaml;

/**
 * Allows to serialize/de-serialize annotations from/to JSON.
 *
 * @see https://github.com/zircote/swagger-php
 */
class Serializer
{
    private static $VALID_ANNOTATIONS = [
        OA\AdditionalProperties::class,
        OA\Attachable::class,
        OA\Components::class,
        OA\Contact::class,
        OA\Delete::class,
        OA\Discriminator::class,
        OA\Examples::class,
        OA\ExternalDocumentation::class,
        OA\Flow::class,
        OA\Get::class,
        OA\Head::class,
        OA\Header::class,
        OA\Info::class,
        OA\Items::class,
        OA\JsonContent::class,
        OA\License::class,
        OA\Link::class,
        OA\MediaType::class,
        OA\OpenApi::class,
        OA\Operation::class,
        OA\Options::class,
        OA\Parameter::class,
        OA\PathParameter::class,
        OA\QueryParameter::class,
        OA\CookieParameter::class,
        OA\HeaderParameter::class,
        OA\Patch::class,
        OA\PathItem::class,
        OA\Post::class,
        OA\Property::class,
        OA\Put::class,
        OA\RequestBody::class,
        OA\Response::class,
        OA\Schema::class,
        OA\SecurityScheme::class,
        OA\Server::class,
        OA\ServerVariable::class,
        OA\Tag::class,
        OA\Trace::class,
        OA\Xml::class,
        OA\XmlContent::class,
    ];

    private const GETTER_PREFIX = 'get';

    /**
     * @param array<string, mixed> $context Sent to the getter function's arguments, allowing functionality similar to https://github.com/Crell/Serde#scopes
     */
    public static function openapiSerialize(object|array $resource, array $context = []): string|array
    {
        if (is_array($resource)) {
            $result = [];

            foreach ($resource as $k => $v) {
                $value = (is_object($v) || is_array($v)) ? self::openapiSerialize($v, $context) : $v;

                $result[$k] = $value;
            }

            return $result;
        }

        if ($resource instanceof \DateTimeInterface) {
            return $resource->format(\DateTimeInterface::RFC3339_EXTENDED);
        }

        $serialized = [];

        foreach (array_merge(self::getReflectionProperties($resource), self::getReflectionFunctions($resource)) as $reflection) {
            $key_value_pair = self::getKeyValuePair($resource, $reflection, $context);

            if ($key_value_pair) {
                [$key, $value] = $key_value_pair;

                $serialized[$key] = $value;
            }
        }

        return $serialized;
    }

    /**
     * @return list<\ReflectionMethod>
     */
    private static function getReflectionFunctions(object $resource): array
    {
        $reflection = new \ReflectionClass($resource);

        $getInterfaceNames = fn (array $carry, \ReflectionClass $rc) => array_merge($carry, $rc->getMethods());

        $interface_reflection_functions = array_reduce($reflection->getInterfaces(), $getInterfaceNames, []);

        $resource_reflection_functions = $reflection->getMethods(\ReflectionProperty::IS_PUBLIC);

        return array_merge($interface_reflection_functions, $resource_reflection_functions);
    }

    /**
     * @return list<\ReflectionProperty>
     */
    private static function getReflectionProperties(object $resource): array
    {
        $reflection = new \ReflectionClass($resource);

        $reflection_properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_READONLY);

        return array_values(
            array_filter($reflection_properties, fn (
                \ReflectionProperty $reflection_property
            ) => !$reflection_property->isStatic())
        );
    }

    private static function getKeyValuePair(object|array $resource, \ReflectionMethod|\ReflectionProperty $reflection, array $context): ?array
    {
        $openapi_reflection_attributes = $reflection->getAttributes(Attributes\Property::class);

        foreach ($openapi_reflection_attributes as $openapi_reflection_attribute) {
            $openapi_attribute = $openapi_reflection_attribute->newInstance();

            $key_value_pair = $reflection instanceof \ReflectionMethod ?
                self::attemptGetter($resource, $context, $openapi_attribute->property) :
                self::attemptProperty($resource, $reflection, $context, $openapi_attribute->property);

            if ($key_value_pair) {
                return $key_value_pair;
            }
        }

        return null;
    }

    /**
     * @param object|array<mixed, mixed> $resource
     * @param array<string, mixed>       $context
     *
     * @return ?array{string, mixed}
     */
    private static function attemptProperty(object|array $resource, \ReflectionProperty $reflection, array $context, string $key): ?array
    {
        if (!is_object($resource)) {
            return null;
        }

        $value = $reflection->getValue($resource);

        if ($key === Generator::UNDEFINED) {
            $key = $reflection->getName();
        }

        if (is_object($value) || is_array($value)) {
            $value = self::openapiSerialize($value, $context);
        }

        return [$key, $value];
    }

    /**
     * @return array<int, mixed>|null If a getter function exists, return the key[0] and value[1], otherwise null
     */
    private static function attemptGetter(object|array $resource, array $context, string $key): ?array
    {
        $found_value = $value = false;

        $normalized_key = ucfirst(self::studly($key));

        $function_name = self::GETTER_PREFIX . $normalized_key;

        $function_name = (string) preg_replace('/[^A-Za-z0-9]/', '', (string) $function_name);

        if (method_exists($resource, $function_name)) {
            $parameters = (new \ReflectionClass($resource))->getMethod($function_name)->getParameters();

            $param_names = array_map(fn ($reflection_parameter) => $reflection_parameter->getName(), $parameters);

            // TODO: Handle/ignore optional params
            $param_filtered_context = array_intersect_key($context, array_flip($param_names));

            if ($param_names == array_keys($param_filtered_context)) {
                $value = $resource->{$function_name}(...$param_filtered_context);

                $found_value = true;
            }
        }

        if ($found_value) {
            $value = (is_object($value) || is_array($value)) ? self::openapiSerialize($value, $context) : $value;

            return [$key, $value];
        }

        return null;
    }

    private static function studly(string $value): string
    {
        $words = explode(' ', str_replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(fn ($word) => ucfirst($word), $words);

        return implode($studlyWords);
    }

    protected static function isValidAnnotationClass(string $className): bool
    {
        return in_array($className, self::$VALID_ANNOTATIONS);
    }

    /**
     * Deserialize a string.
     */
    public function deserialize(string $jsonString, string $className): OA\AbstractAnnotation
    {
        if (!$this->isValidAnnotationClass($className)) {
            throw new \Exception($className . ' is not defined in OpenApi PHP Annotations');
        }

        return $this->doDeserialize(json_decode($jsonString), $className, new Context(['generated' => true]));
    }

    /**
     * Deserialize a file.
     */
    public function deserializeFile(string $filename, string $format = 'json', string $className = OA\OpenApi::class): OA\AbstractAnnotation
    {
        if (!$this->isValidAnnotationClass($className)) {
            throw new \Exception($className . ' is not a valid OpenApi PHP Annotations');
        }

        $contents = file_get_contents($filename);

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ('yaml' == $format || in_array($ext, ['yml', 'yaml'])) {
            $contents = json_encode(Yaml::parse($contents));
        }

        return $this->doDeserialize(json_decode($contents), $className, new Context(['generated' => true]));
    }

    /**
     * Do deserialization.
     */
    protected function doDeserialize(\stdClass $c, string $class, Context $context): OA\AbstractAnnotation
    {
        $annotation = new $class(['_context' => $context]);
        foreach ((array) $c as $property => $value) {
            if ($property === '$ref') {
                $property = 'ref';
            }

            if (substr($property, 0, 2) === 'x-') {
                if (Generator::isDefault($annotation->x)) {
                    $annotation->x = [];
                }
                $custom = substr($property, 2);
                $annotation->x[$custom] = $value;
            } else {
                $annotation->{$property} = $this->doDeserializeProperty($annotation, $property, $value, $context);
            }
        }

        if ($annotation instanceof OA\OpenApi) {
            $context->root()->version = $annotation->openapi;
        }

        return $annotation;
    }

    /**
     * Deserialize the annotation's property.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function doDeserializeProperty(OA\AbstractAnnotation $annotation, string $property, $value, Context $context)
    {
        // property is primitive type
        if (array_key_exists($property, $annotation::$_types)) {
            return $this->doDeserializeBaseProperty($annotation::$_types[$property], $value, $context);
        }

        // property is embedded annotation
        // note: this does not support custom nested annotation classes
        foreach ($annotation::$_nested as $nestedClass => $declaration) {
            // property is an annotation
            if (is_string($declaration) && $declaration === $property) {
                if (is_object($value)) {
                    return $this->doDeserialize($value, $nestedClass, $context);
                } else {
                    return $value;
                }
            }

            // property is an annotation array
            if (is_array($declaration) && count($declaration) === 1 && $declaration[0] === $property) {
                $annotationArr = [];
                foreach ($value as $v) {
                    $annotationArr[] = $this->doDeserialize($v, $nestedClass, $context);
                }

                return $annotationArr;
            }

            // property is an annotation hash map
            if (is_array($declaration) && count($declaration) === 2 && $declaration[0] === $property) {
                $key = $declaration[1];
                $annotationHash = [];
                foreach ($value as $k => $v) {
                    $annotation = $this->doDeserialize($v, $nestedClass, $context);
                    $annotation->{$key} = $k;
                    $annotationHash[$k] = $annotation;
                }

                return $annotationHash;
            }
        }

        return $value;
    }

    /**
     * Deserialize base annotation property.
     *
     * @param array|string $type  The property type
     * @param mixed        $value The value to deserialization
     *
     * @return array|OA\AbstractAnnotation
     */
    protected function doDeserializeBaseProperty($type, $value, Context $context)
    {
        $isAnnotationClass = is_string($type) && is_subclass_of(trim($type, '[]'), OA\AbstractAnnotation::class);

        if ($isAnnotationClass) {
            $isArray = strpos($type, '[') === 0 && substr($type, -1) === ']';

            if ($isArray) {
                $annotationArr = [];
                $class = trim($type, '[]');

                foreach ($value as $v) {
                    $annotationArr[] = $this->doDeserialize($v, $class, $context);
                }

                return $annotationArr;
            }

            return $this->doDeserialize($value, $type, $context);
        }

        return $value;
    }
}
