<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations as OA;
use Symfony\Component\Yaml\Yaml;

/**
 * Allows to serialize/de-serialize annotations from/to JSON.
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
        OA\Webhook::class,
        OA\Xml::class,
        OA\XmlContent::class,
    ];

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
            throw new OpenApiException($className . ' is not defined in OpenApi PHP Annotations');
        }

        return $this->doDeserialize(json_decode($jsonString), $className, new Context(['generated' => true]));
    }

    /**
     * Deserialize a file.
     */
    public function deserializeFile(string $filename, string $format = 'json', string $className = OA\OpenApi::class): OA\AbstractAnnotation
    {
        if (!$this->isValidAnnotationClass($className)) {
            throw new OpenApiException($className . ' is not a valid OpenApi PHP Annotations');
        }

        $contents = file_get_contents($filename);

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ('yaml' === $format || in_array($ext, ['yml', 'yaml'])) {
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
