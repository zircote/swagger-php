<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Validation;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\OpenApiException;

/**
 * Generic default validator for all annotations.
 *
 * @extends AbstractValidator<OA\Schema>
 */
class DefaultValidator extends AbstractValidator
{
    public function validate(Analysis $analysis, OA\AbstractAnnotation $root, \stdClass $context): bool
    {
        $isValid = $this->validateUnmerged($root);
        $isValid = $this->validateConflictingKeys($root) && $isValid;
        $isValid = $this->validateRefs($analysis, $root) && $isValid;
        $isValid = $this->validateRequiredFields($root) && $isValid;
        $isValid = $this->validateTypes($root) && $isValid;
        $isValid = $this->validateExamples($root) && $isValid;

        return $isValid;
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

        if (is_subclass_of($type, OA\AbstractAnnotation::class)) {
            $type = 'object';
        }

        $isValidType = fn (string $type, mixed $value): bool => match ($type) {
            'string' => is_string($value),
            'boolean' => is_bool($value),
            'integer' => is_int($value),
            'number' => is_numeric($value),
            'object' => is_object($value),
            'array' => is_array($value) && array_is_list($value),
            'scheme' => in_array($value, ['http', 'https', 'ws', 'wss'], true),
            default => throw new OpenApiException('Invalid type "' . $type . '"'),
        };

        foreach (explode('|', $type) as $tt) {
            if ($isValidType(trim($tt), $value)) {
                return true;
            }
        }

        return false;
    }

    protected function validateUnmerged(OA\AbstractAnnotation $root): bool
    {
        $isValid = true;

        foreach ($root->_unmerged as $annotation) {
            if (!is_object($annotation)) {
                $this->logger->warning('Unexpected type: "' . gettype($annotation) . '" in ' . $root->identity() . '->_unmerged, expecting a Annotation object');
                break;
            }

            if ($details = $root->matchNested($annotation)) {
                $property = $details->value;
                if (is_array($property)) {
                    $this->logger->warning('Only one ' . OA\AbstractAnnotation::shorten($annotation::class) . '() allowed for ' . $root->identity() . ' multiple found, skipped: ' . $annotation->_context);
                } else {
                    $this->logger->warning('Only one ' . OA\AbstractAnnotation::shorten($annotation::class) . '() allowed for ' . $root->identity() . " multiple found in:\n    Using: " . $root->{$property}->_context . "\n  Skipped: " . $annotation->_context);
                }
            } elseif ($annotation instanceof OA\AbstractAnnotation) {
                $message = 'Unexpected ' . $annotation->identity();
                if ($annotation::$_parents) {
                    $message .= ', expected to be inside ' . implode(', ', OA\AbstractAnnotation::shorten($annotation::$_parents));
                }
                $this->logger->warning($message . ' in ' . $annotation->_context);
            }

            $isValid = false;
        }

        return $isValid;
    }

    protected function validateConflictingKeys(OA\AbstractAnnotation $root): bool
    {
        $isValid = true;

        foreach ($root::$_nested as $annotationClass => $nested) {
            if (is_string($nested) || count($nested) === 1) {
                continue;
            }
            $property = $nested[0];
            if (Generator::isDefault($root->{$property})) {
                continue;
            }
            $keys = [];
            $keyField = $nested[1];
            /** @var OA\AbstractAnnotation $item */
            foreach ($root->{$property} as $key => $item) {
                if (is_array($item) && !is_numeric($key)) {
                    $this->logger->warning($root->identity() . '->' . $property . ' is an object literal, use nested ' . OA\AbstractAnnotation::shorten($annotationClass) . '() annotation(s) in ' . $root->_context);
                    $keys[$key] = $item;
                } elseif (Generator::isDefault($item->{$keyField})) {
                    $this->logger->error($item->identity() . ' is missing key-field: "' . $keyField . '" in ' . $item->_context);
                } elseif (isset($keys[$item->{$keyField}])) {
                    $this->logger->error('Multiple ' . $item->identity([]) . ' with the same ' . $keyField . '="' . $item->{$keyField} . "\":\n  " . $item->_context . "\n  " . $keys[$item->{$keyField}]->_context);
                } else {
                    $keys[$item->{$keyField}] = $item;
                }
            }
        }

        return $isValid;
    }

    protected function validateRefs(Analysis $analysis, OA\AbstractAnnotation $root): bool
    {
        if (!property_exists($root, 'ref') || Generator::isDefault($root->ref) || !is_string($root->ref)) {
            return true;
        }

        if (str_starts_with($root->ref, '#/') && $analysis->openapi) {
            try {
                $analysis->openapi->ref($root->ref);
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage() . ' for ' . $root->identity() . ' in ' . $root->_context, ['exception' => $e]);
            }
        }

        return true;
    }

    protected function validateRequiredFields(OA\AbstractAnnotation $root): bool
    {
        if (property_exists($root, 'ref') && !Generator::isDefault($root->ref) && is_string($root->ref)) {
            return true;
        }

        foreach ($root::$_required as $property) {
            if (Generator::isDefault($root->{$property})) {
                $message = 'Missing required field "' . $property . '" for ' . $root->identity() . ' in ' . $root->_context;
                foreach ($root::$_nested as $class => $nested) {
                    $nestedProperty = is_array($nested) ? $nested[0] : $nested;
                    if ($property === $nestedProperty) {
                        if ($root instanceof OA\OpenApi) {
                            $message = 'Required ' . OA\AbstractAnnotation::shorten($class) . '() not found';
                        } elseif (is_array($nested)) {
                            $message = $root->identity() . ' requires at least one ' . OA\AbstractAnnotation::shorten($class) . '() in ' . $root->_context;
                        } else {
                            $message = $root->identity() . ' requires a ' . OA\AbstractAnnotation::shorten($class) . '() in ' . $root->_context;
                        }
                        break;
                    }
                }
                $this->logger->warning($message);
            }
        }

        return true;
    }

    protected function validateTypes(OA\AbstractAnnotation $root): bool
    {
        $isValid = true;

        foreach ($root::$_types as $property => $type) {
            $value = $root->{$property};
            if (Generator::isDefault($value) || $value === null) {
                continue;
            }
            if (is_string($type)) {
                if (!$this->validateValueType($type, $value)) {
                    $this->logger->warning($root->identity() . '->' . $property . ' is a "' . gettype($value) . '", expecting a "' . $type . '" in ' . $root->_context);
                    $isValid = false;
                }
            } elseif (is_array($type)) { // enum?
                if (!in_array($value, $type)) {
                    $this->logger->warning($root->identity() . '->' . $property . ' "' . $value . '" is invalid, expecting "' . implode('", "', $type) . '" in ' . $root->_context);
                }
            } else {
                throw new OpenApiException('Invalid ' . $root::class . '::$_types[' . $property . ']');
            }
        }

        return $isValid;
    }

    protected function validateExamples(OA\AbstractAnnotation $root): bool
    {
        if (property_exists($root, 'example') && property_exists($root, 'examples')) {
            if (!Generator::isDefault($root->example) && !Generator::isDefault($root->examples)) {
                $this->logger->warning($root->identity() . ': "example" and "examples" are mutually exclusive');

                return false;
            }
        }

        return true;
    }
}
