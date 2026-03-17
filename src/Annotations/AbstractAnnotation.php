<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\OpenApiException;
use Symfony\Component\Yaml\Yaml;

/**
 * The openapi annotation base class.
 */
abstract class AbstractAnnotation implements \JsonSerializable
{
    /**
     * While the OpenAPI Specification tries to accommodate most use cases, additional data can be added to extend the specification at certain points.
     * For further details see https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#specificationExtensions
     * The keys inside the array will be prefixed with <code>x-</code>.
     *
     * @var array<string,mixed>
     */
    public $x = Generator::UNDEFINED;

    /**
     * Arbitrary attachables for this annotation.
     * These will be ignored but can be used for custom processing.
     *
     * @var array
     */
    public $attachables = Generator::UNDEFINED;

    public ?Context $_context;

    /**
     * Annotations that couldn't be merged by mapping or postprocessing.
     *
     * @var array
     */
    public $_unmerged = [];

    /**
     * The properties which are required by [the spec](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md).
     *
     * @var list<string>
     */
    public static $_required = [];

    /**
     * Specify the type of the property.
     *
     * Examples:
     *   'name' => 'string'         // a string
     *   'required' => 'boolean',   // true or false
     *   'tags' => '[string]',      // string array
     *   'in' => ["query", "header", "path", "formData", "body"] // must be one on these
     *   'oneOf' => [Schema::class] // array of schema objects.
     *
     * @var array<string,string|array<string>>
     */
    public static $_types = [];

    /**
     * Declarative mapping of Annotation types to properties.
     *
     * Examples:
     *   Info::class => 'info',                // Set @OA\Info annotation as the info property.
     *   Parameter::class => ['parameters'],   // Append @OA\Parameter annotations the parameters list.
     *   PathItem::class => ['paths', 'path'], // Add @OA\PathItem annotation to the `paths` map and use `path` as key.
     *
     * @var array<class-string<AbstractAnnotation>,string|array<string>>
     */
    public static $_nested = [];

    /**
     * Reverse mapping of $_nested with the allowed parent annotations.
     *
     * @var array<class-string<AbstractAnnotation>>
     */
    public static $_parents = [];

    /**
     * Properties that are blacklisted from the JSON output.
     *
     * @var array<string>
     */
    public static $_blacklist = ['_context', '_unmerged', '_analysis', 'attachables'];

    public function __construct(array $properties)
    {
        if (isset($properties['_context'])) {
            $this->_context = $properties['_context'];
            unset($properties['_context']);
        } elseif (Generator::$context) {
            $this->_context = Generator::$context;
        } else {
            $this->_context = new Context(['generated' => true]);
        }

        if ($this->_context->is('annotations') === false) {
            $this->_context->annotations = [];
        }

        $this->_context->annotations[] = $this;
        $nestedContext = new Context(['nested' => $this], $this->_context);
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
                if (is_array($value)) {
                    foreach ($value as $key => $annotation) {
                        if ($annotation instanceof AbstractAnnotation) {
                            $this->{$property}[$key] = $this->nested($annotation, $nestedContext);
                        }
                    }
                }
            } elseif ($property !== 'value') {
                $this->{$property} = $value;
            } elseif (is_array($value)) {
                $annotations = [];
                foreach ($value as $annotation) {
                    if ($annotation instanceof AbstractAnnotation) {
                        $annotations[] = $annotation;
                    } else {
                        $this->_context->logger->warning('Unexpected field in ' . $this->identity() . ' in ' . $this->_context);
                    }
                }
                $this->merge($annotations);
            } elseif (is_object($value)) {
                $this->merge([$value]);
            } else {
                if (!Generator::isDefault($value)) {
                    $this->_context->logger->warning('Unexpected parameter "' . $property . '" in ' . $this->identity());
                }
            }
        }
    }

    /**
     * Merge given annotations to their mapped properties configured in static::$_nested.
     *
     * Annotations that couldn't be merged are added to the _unmerged array.
     *
     * @param list<AbstractAnnotation> $annotations
     * @param bool                     $ignore      Ignore unmerged annotations
     *
     * @return list<AbstractAnnotation> The unmerged annotations
     */
    public function merge(array $annotations, bool $ignore = false): array
    {
        $unmerged = [];
        $nestedContext = new Context(['nested' => $this], $this->_context);

        foreach ($annotations as $annotation) {
            $mapped = false;
            if ($details = $this->matchNested($annotation)) {
                $property = $details->value;
                if (is_array($property)) {
                    $property = $property[0];
                    if (Generator::isDefault($this->{$property})) {
                        $this->{$property} = [];
                    }
                    $this->{$property}[] = $this->nested($annotation, $nestedContext);
                    $mapped = true;
                } elseif (Generator::isDefault($this->{$property})) {
                    // ignore duplicate nested if only one expected
                    $this->{$property} = $this->nested($annotation, $nestedContext);
                    $mapped = true;
                }
            }
            if (!$mapped) {
                $unmerged[] = $annotation;
            }
        }
        if (!$ignore) {
            foreach ($unmerged as $annotation) {
                $this->_unmerged[] = $this->nested($annotation, $nestedContext);
            }
        }

        return $unmerged;
    }

    /**
     * Merge the properties from the given object into this annotation.
     * Prevents overwriting properties that are already configured.
     *
     * @param object $object
     */
    public function mergeProperties($object): void
    {
        $currentValues = get_object_vars($this);
        foreach ($object as $property => $value) {
            if ($property === '_context') {
                continue;
            }
            if (Generator::isDefault($currentValues[$property])) {
                // Overwrite default values
                $this->{$property} = $value;
                continue;
            }
            if ($property === '_unmerged') {
                $this->_unmerged = array_merge($this->_unmerged, $value);
                continue;
            }
            if ($currentValues[$property] !== $value) {
                // New value is not the same?
                if (Generator::isDefault($value)) {
                    continue;
                }
                $identity = method_exists($object, 'identity') ? $object->identity() : $object::class;
                $context1 = $this->_context;
                $context2 = property_exists($object, '_context') ? $object->_context : 'unknown';
                if ($this->{$property} instanceof AbstractAnnotation) {
                    $context1 = $this->{$property}->_context;
                }
                $this->_context->logger->error('Multiple definitions for ' . $identity . '->' . $property . "\n     Using: " . $context1 . "\n  Skipping: " . $context2);
            }
        }
    }

    /**
     * Generate the documentation in YAML format.
     *
     * @param int-mask-of<Yaml::PARSE_*>|null $flags A bit field of PARSE_* constants to customize the YAML parser behavior
     */
    public function toYaml(?int $flags = null): string
    {
        if ($flags === null) {
            $flags = Yaml::DUMP_OBJECT_AS_MAP ^ Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE;
        }

        return Yaml::dump(json_decode($this->toJson(JSON_INVALID_UTF8_IGNORE)), 10, 2, $flags);
    }

    /**
     * Generate the documentation in JSON format.
     */
    public function toJson(?int $flags = null): string
    {
        if ($flags === null) {
            $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE;
        }

        return json_encode($this, $flags);
    }

    public function __debugInfo()
    {
        $properties = [];
        foreach (get_object_vars($this) as $property => $value) {
            if (!Generator::isDefault($value)) {
                $properties[$property] = $value;
            }
        }

        return $properties;
    }

    public function jsonSerialize(): \stdClass
    {
        $data = new \stdClass();

        // Strip undefined values.
        foreach (get_object_vars($this) as $property => $value) {
            if (!Generator::isDefault($value)) {
                $data->{$property} = $value;
            }
        }

        // Strip properties that are for internal (swagger-php) use.
        foreach (static::$_blacklist as $property) {
            unset($data->{$property});
        }

        // Correct empty array to empty objects.
        foreach (static::$_types as $property => $type) {
            if ($type === 'object' && is_array($data->{$property}) && $data->{$property} === []) {
                $data->{$property} = new \stdClass();
            }
        }

        // Inject vendor properties.
        unset($data->x);
        if (is_array($this->x)) {
            foreach ($this->x as $property => $value) {
                $prefixed = 'x-' . $property;
                $data->{$prefixed} = $value;
            }
        }

        // Map nested keys
        foreach (static::$_nested as $nested) {
            if (is_string($nested) || count($nested) === 1) {
                continue;
            }
            $property = $nested[0];
            if (Generator::isDefault($this->{$property})) {
                continue;
            }
            $keyField = $nested[1];
            $object = new \stdClass();
            foreach ($this->{$property} as $key => $item) {
                if (is_numeric($key) === false && is_array($item)) {
                    $object->{$key} = $item;
                } else {
                    $key = $item->{$keyField};
                    if (!Generator::isDefault($key) && empty($object->{$key})) {
                        $object->{$key} = $item instanceof \JsonSerializable ? $item->jsonSerialize() : $item;
                        unset($object->{$key}->{$keyField});
                    }
                }
            }
            $data->{$property} = $object;
        }

        // $ref
        if (isset($data->ref)) {
            // Only specific https://github.com/OAI/OpenAPI-Specification/blob/3.1.0/versions/3.1.0.md#reference-object
            $ref = ['$ref' => $data->ref];
            if (!$this->_context->isVersion('3.0.x')) {
                foreach (['summary', 'description'] as $prop) {
                    if (property_exists($data, $prop)) {
                        $ref[$prop] = $data->{$prop};
                    }
                }
            }
            if (property_exists($this, 'nullable') && $this->nullable === true) {
                $ref = ['oneOf' => [$ref]];
                if (!$this->_context->isVersion('3.0.x')) {
                    $ref['oneOf'][] = ['type' => 'null'];
                } else {
                    $ref['nullable'] = $data->nullable;
                }
                unset($data->ref, $data->nullable);

                // preserve other properties
                foreach (get_object_vars($data) as $property => $value) {
                    $ref[$property] = $value;
                }
            }
            $data = (object) $ref;
        }

        if ($this->_context->isVersion('3.0.x')) {
            if (isset($data->exclusiveMinimum) && is_numeric($data->exclusiveMinimum)) {
                $data->minimum = $data->exclusiveMinimum;
                $data->exclusiveMinimum = true;
            }

            if (isset($data->exclusiveMaximum) && is_numeric($data->exclusiveMaximum)) {
                $data->maximum = $data->exclusiveMaximum;
                $data->exclusiveMaximum = true;
            }

            if (isset($data->type) && is_array($data->type)) {
                if (in_array('null', $data->type)) {
                    $data->nullable = true;
                    $data->type = array_filter($data->type, static fn ($v): bool => $v !== 'null');
                    if (1 === count($data->type)) {
                        $data->type = array_pop($data->type);
                    }
                }
            }
            if (isset($data->type) && is_array($data->type)) {
                if (1 === count($data->type)) {
                    $data->type = array_pop($data->type);
                } else {
                    unset($data->type);
                }
            }

            unset($data->unevaluatedProperties);
        }

        if (!$this->_context->isVersion('3.0.x')) {
            if (isset($data->nullable)) {
                if (true === $data->nullable) {
                    if (isset($data->oneOf)) {
                        $data->oneOf[] = ['type' => 'null'];
                    } elseif (isset($data->anyOf)) {
                        $data->anyOf[] = ['type' => 'null'];
                    } elseif (isset($data->allOf)) {
                        $data->allOf[] = ['type' => 'null'];
                    } elseif (isset($data->type)) {
                        $data->type = (array) $data->type;
                        $data->type[] = 'null';
                    }
                }
                unset($data->nullable);
            }

            if (isset($data->minimum) && isset($data->exclusiveMinimum)) {
                if (true === $data->exclusiveMinimum) {
                    $data->exclusiveMinimum = $data->minimum;
                    unset($data->minimum);
                } elseif (false === $data->exclusiveMinimum) {
                    unset($data->exclusiveMinimum);
                }
            }

            if (isset($data->maximum) && isset($data->exclusiveMaximum)) {
                if (true === $data->exclusiveMaximum) {
                    $data->exclusiveMaximum = $data->maximum;
                    unset($data->maximum);
                } elseif (false === $data->exclusiveMaximum) {
                    unset($data->exclusiveMaximum);
                }
            }
        }

        return $data;
    }

    /**
     * Validate a given value against a `_$type` definition.
     */
    private function validateValueType(string $type, mixed $value): bool
    {
        if (str_starts_with($type, '[') && str_ends_with($type, ']')) {
            // $value must be an array
            if (!$this->validateValueType('array', $value)) {
                return false;
            }

            $itemType = substr($type, 1, -1);
            foreach ($value as $item) {
                if (!$this->validateValueType($itemType, $item)) {
                    return false;
                }
            }

            return true;
        }

        if (is_subclass_of($type, AbstractAnnotation::class)) {
            $type = 'object';
        }

        $isValidType = fn (string $type, mixed $value): bool => match ($type) {
            'string' => is_string($value),
            'boolean' => is_bool($value),
            'integer' => is_int($value),
            'number' => is_numeric($value),
            'object' => is_object($value),
            'array' => is_array($value) && array_is_list($value),
            'scheme' => in_array($value, ['http', 'https', 'ws', 'wss'], strict: true),
            default => throw new OpenApiException('Invalid type "' . $type . '"'),
        };

        foreach (explode('|', $type) as $tt) {
            if ($isValidType(trim($tt), $value)) {
                return true;
            }
        }

        return false;
    }

    public function validate(?Analysis $analysis = null, string $version = OpenApi::DEFAULT_VERSION, ?object $context = null): bool
    {
        $isValid = true;

        // validate unmerged
        foreach ($this->_unmerged as $annotation) {
            if (!is_object($annotation)) {
                $this->_context->logger->warning('Unexpected type: "' . gettype($annotation) . '" in ' . $this->identity() . '->_unmerged, expecting a Annotation object');
                break;
            }

            if ($details = $this->matchNested($annotation)) {
                $property = $details->value;
                if (is_array($property)) {
                    $this->_context->logger->warning('Only one ' . $annotation->identity([]) . ' allowed for ' . $this->identity() . ' multiple found, skipped: ' . $annotation->_context);
                } else {
                    $this->_context->logger->warning('Only one ' . $annotation->identity([]) . ' allowed for ' . $this->identity() . " multiple found in:\n    Using: " . $this->{$property}->_context . "\n  Skipped: " . $annotation->_context);
                }
            } elseif ($annotation instanceof AbstractAnnotation) {
                $message = 'Unexpected ' . $annotation->identity();
                if ($annotation::$_parents) {
                    $message .= ', expected to be inside ' . implode(', ', AbstractAnnotation::shorten($annotation::$_parents));
                }
                $this->_context->logger->warning($message . ' in ' . $annotation->_context);
            }

            $isValid = false;
        }

        // validate conflicting keys
        foreach ($this::$_nested as $annotationClass => $nested) {
            if (is_string($nested) || count($nested) === 1) {
                continue;
            }
            $property = $nested[0];
            if (Generator::isDefault($this->{$property})) {
                continue;
            }
            $keys = [];
            $keyField = $nested[1];
            /** @var AbstractAnnotation $item */
            foreach ($this->{$property} as $key => $item) {
                if (is_array($item) && !is_numeric($key)) {
                    $this->_context->logger->warning($this->identity() . '->' . $property . ' is an object literal, use nested ' . AbstractAnnotation::shorten($annotationClass) . '() annotation(s) in ' . $this->_context);
                    $keys[$key] = $item;
                } elseif (Generator::isDefault($item->{$keyField})) {
                    $this->_context->logger->error($item->identity() . ' is missing key-field: "' . $keyField . '" in ' . $item->_context);
                } elseif (isset($keys[$item->{$keyField}])) {
                    $this->_context->logger->error('Multiple ' . $item->identity([]) . ' with the same ' . $keyField . '="' . $item->{$keyField} . "\":\n  " . $item->_context . "\n  " . $keys[$item->{$keyField}]->_context);
                } else {
                    $keys[$item->{$keyField}] = $item;
                }
            }
        }

        // validate refs
        if ($analysis?->openapi && property_exists($this, 'ref') && !Generator::isDefault($this->ref) && is_string($this->ref)) {
            if (str_starts_with($this->ref, '#/')) {
                try {
                    $analysis->openapi->ref($this->ref);
                } catch (\Exception $e) {
                    $this->_context->logger->warning($e->getMessage() . ' for ' . $this->identity() . ' in ' . $this->_context, ['exception' => $e]);
                    $isValid = false;
                }
            }
        }

        // validate required properties
        if (!property_exists($this, 'ref') || Generator::isDefault($this->ref) || !is_string($this->ref)) {
            foreach ($this::$_required as $property) {
                if (Generator::isDefault($this->{$property})) {
                    $message = 'Missing required field "' . $property . '" for ' . $this->identity() . ' in ' . $this->_context;
                    foreach ($this::$_nested as $class => $nested) {
                        $nestedProperty = is_array($nested) ? $nested[0] : $nested;
                        if ($property === $nestedProperty) {
                            if ($this instanceof OpenApi) {
                                $message = 'Required ' . AbstractAnnotation::shorten($class) . '() not found';
                            } elseif (is_array($nested)) {
                                $message = $this->identity() . ' requires at least one ' . AbstractAnnotation::shorten($class) . '() in ' . $this->_context;
                            } else {
                                $message = $this->identity() . ' requires a ' . AbstractAnnotation::shorten($class) . '() in ' . $this->_context;
                            }
                            break;
                        }
                    }
                    $this->_context->logger->warning($message);
                }
            }
        }

        // validate types
        foreach ($this::$_types as $property => $type) {
            $value = $this->{$property};
            if (Generator::isDefault($value) || $value === null) {
                continue;
            }
            if (is_string($type)) {
                if (!$this->validateValueType($type, $value)) {
                    $this->_context->logger->warning($this->identity() . '->' . $property . ' is a "' . gettype($value) . '", expecting a "' . $type . '" in ' . $this->_context);
                    $isValid = false;
                }
            } elseif (is_array($type)) { // enum?
                if (!in_array($value, $type)) {
                    $this->_context->logger->warning($this->identity() . '->' . $property . ' "' . $value . '" is invalid, expecting "' . implode('", "', $type) . '" in ' . $this->_context);
                }
            } else {
                throw new OpenApiException('Invalid ' . static::class . '::$_types[' . $property . ']');
            }
        }

        // validate example/examples
        if (property_exists($this, 'example') && property_exists($this, 'examples')) {
            if (!Generator::isDefault($this->example) && !Generator::isDefault($this->examples)) {
                $this->_context->logger->warning($this->identity() . ': "example" and "examples" are mutually exclusive');

                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * Return a simple string representation of the annotation.
     *
     * @param array|null $properties the properties to include in the string representation
     * @example "@OA\Response(response=200)"
     */
    public function identity(?array $properties = null): string
    {
        $class = static::class;

        if (null === $properties) {
            $properties = [];
            /** @var class-string<AbstractAnnotation> $parent */
            foreach (static::$_parents as $parent) {
                foreach ($parent::$_nested as $annotationClass => $entry) {
                    if ($annotationClass === $class && is_array($entry) && !Generator::isDefault($this->{$entry[1]})) {
                        $properties[] = $entry[1];
                        break 2;
                    }
                }
            }
        }

        $details = [];
        foreach ($properties as $property) {
            $value = $this->{$property};
            if ($value !== null && !Generator::isDefault($value)) {
                $details[] = $property . '=' . (is_string($value) ? '"' . $value . '"' : $value);
            }
        }

        return static::shorten(static::class) . '(' . implode(',', $details) . ')';
    }

    /**
     * Check if <code>$other</code> can be nested, and if so, return details about where/how.
     *
     * @param AbstractAnnotation $other the other annotation
     *
     * @return null|object key/value object or <code>null</code>
     */
    public function matchNested($other)
    {
        if ($other instanceof AbstractAnnotation && array_key_exists($root = $other->getRoot(), static::$_nested)) {
            return (object) ['key' => $root, 'value' => static::$_nested[$root]];
        }

        return null;
    }

    /**
     * Get the root annotation.
     *
     * This is used for resolving type equality and nesting rules to allow those rules to also work for custom,
     * derived annotation classes.
     *
     * @return class-string the root annotation class in the <code>OpenApi\\Annotations</code> namespace
     */
    public function getRoot(): string
    {
        $class = static::class;

        do {
            if (str_starts_with($class, 'OpenApi\\Annotations\\')) {
                break;
            }
        } while ($class = get_parent_class($class));

        return $class;
    }

    /**
     * Match the annotation root.
     *
     * @param class-string $thisClass the root class to match
     */
    public function isRoot(string $thisClass): bool
    {
        return static::class === $thisClass || $this->getRoot() === $thisClass;
    }

    /**
     * Wrap the context with a reference to the annotation it is nested in.
     */
    protected function nested(AbstractAnnotation $annotation, Context $nestedContext): self
    {
        if (property_exists($annotation, '_context') && $annotation->_context === $this->_context) {
            $annotation->_context = $nestedContext;
        }

        return $annotation;
    }

    protected function combine(...$args): array
    {
        $combined = [];
        foreach ($args as $arg) {
            if (is_array($arg)) {
                $combined = array_merge($combined, $arg);
            } else {
                $combined[] = $arg;
            }
        }

        return array_filter($combined, static fn ($value): bool => !Generator::isDefault($value) && $value !== null);
    }

    /**
     * Shorten class name(s).
     *
     * @param array|object|string $classes Class(es) to shorten
     *
     * @return string|list<string> One or more shortened class names
     */
    protected static function shorten($classes)
    {
        $short = [];
        foreach ((array) $classes as $class) {
            $short[] = '@' . str_replace([
                    'OpenApi\\Annotations\\',
                    'OpenApi\\Attributes\\',
                ], 'OA\\', (string) $class);
        }

        return is_array($classes) ? $short : array_pop($short);
    }
}
