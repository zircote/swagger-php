<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class AugmentProperties implements ProcessorInterface
{
    use Concerns\DocblockTrait;
    use Concerns\RefTrait;
    use Concerns\TypesTrait;

    public function __invoke(Analysis $analysis)
    {
        $refs = [];
        if (!Generator::isDefault($analysis->openapi->components) && !Generator::isDefault($analysis->openapi->components->schemas)) {
            foreach ($analysis->openapi->components->schemas as $schema) {
                if (!Generator::isDefault($schema->schema)) {
                    $refKey = $this->toRefKey($schema->_context, $schema->_context->class);
                    $refs[$refKey] = OA\Components::ref($schema);
                }
            }
        }

        /** @var OA\Property[] $properties */
        $properties = $analysis->getAnnotationsOfType(OA\Property::class);

        foreach ($properties as $property) {
            $context = $property->_context;

            if (Generator::isDefault($property->property)) {
                $property->property = $context->property;
            }

            if (!Generator::isDefault($property->ref)) {
                continue;
            }

            $typeAndDescription = $this->extractVarTypeAndDescription((string) $context->comment);

            if (Generator::isDefault($property->type)) {
                $this->augmentType($analysis, $property, $context, $refs, $typeAndDescription['type']);
            } else {
                if (!is_array($property->type)) {
                    $this->mapNativeType($property, $property->type);
                }
            }

            if (Generator::isDefault($property->description) && $typeAndDescription['description']) {
                $property->description = trim($typeAndDescription['description']);
            }
            if (Generator::isDefault($property->description) && $this->isRoot($property)) {
                $property->description = $this->extractContent($context->comment);
            }

            if (Generator::isDefault($property->example) && ($example = $this->extractExampleDescription((string) $context->comment))) {
                $property->example = $example;
            }

            if (Generator::isDefault($property->deprecated) && ($deprecated = $this->isDeprecated($context->comment))) {
                $property->deprecated = $deprecated;
            }
        }
    }

    protected function augmentType(Analysis $analysis, OA\Property $property, Context $context, array $refs, ?string $varType): void
    {
        // docblock typehints
        if ($varType) {
            $allTypes = strtolower(trim($varType));

            if ($this->isNullable($allTypes) && Generator::isDefault($property->nullable)) {
                $property->nullable = true;
            }

            $allTypes = $this->stripNull($allTypes);
            preg_match('/^([^\[\<]+)(.*$)/', $allTypes, $typeMatches);
            $type = $typeMatches[1];

            // finalise property type/ref
            if (!$this->mapNativeType($property, $type)) {
                $refKey = $this->toRefKey($context, $type);
                if (Generator::isDefault($property->ref) && array_key_exists($refKey, $refs)) {
                    $property->ref = $refs[$refKey];
                }
            }

            // ok, so we possibly have a type or ref
            if (!Generator::isDefault($property->ref) && $typeMatches[2] === '' && !Generator::isDefault($property->nullable) && $property->nullable) {
                $refKey = $this->toRefKey($context, $type);
                $property->oneOf = [
                    $schema = new OA\Schema([
                        'ref' => $refs[$refKey],
                        '_context' => new Context(['generated' => true], $property->_context),
                    ]),
                ];
                $analysis->addAnnotation($schema, $schema->_context);
                $property->nullable = true;
            } elseif ($typeMatches[2] === '[]') {
                if (Generator::isDefault($property->items)) {
                    $property->items = $items = new OA\Items(
                        [
                            'type' => $property->type,
                            '_context' => new Context(['generated' => true], $context),
                        ]
                    );
                    $analysis->addAnnotation($items, $items->_context);
                    if (!Generator::isDefault($property->ref)) {
                        $property->items->ref = $property->ref;
                        $property->ref = Generator::UNDEFINED;
                    }
                    $property->type = 'array';
                }
            } elseif ($property->type === 'integer' && str_starts_with($typeMatches[2], '<') && str_ends_with($typeMatches[2], '>')) {
                [$min, $max] = explode(',', substr($typeMatches[2], 1, -1));

                if (is_numeric($min)) {
                    $property->minimum = (int) $min;
                }
                if (is_numeric($max)) {
                    $property->maximum = (int) $max;
                }
            } elseif ($type === 'positive-int') {
                $property->type = 'integer';
                $property->minimum = 1;
            } elseif ($type === 'negative-int') {
                $property->type = 'integer';
                $property->maximum = -1;
            } elseif ($type === 'non-positive-int') {
                $property->type = 'integer';
                $property->maximum = 0;
            } elseif ($type === 'non-negative-int') {
                $property->type = 'integer';
                $property->minimum = 0;
            } elseif ($type === 'non-zero-int') {
                $property->type = 'integer';
                if ($property->_context->isVersion(OA\OpenApi::VERSION_3_1_0)) {
                    $property->not = ['const' => 0];
                } else {
                    $property->not = ['enum' => [0]];
                }
            }
        }

        // native typehints
        if ($context->type && !Generator::isDefault($context->type)) {
            if ($context->nullable === true) {
                $property->nullable = true;
            }
            $type = strtolower($context->type);
            if (!$this->mapNativeType($property, $type)) {
                $refKey = $this->toRefKey($context, $type);
                if (Generator::isDefault($property->ref) && array_key_exists($refKey, $refs)) {
                    $this->applyRef($analysis, $property, $refs[$refKey]);
                } else {
                    if (is_string($context->type) && $typeSchema = $analysis->getSchemaForSource($context->type)) {
                        if (Generator::isDefault($property->format)) {
                            $property->ref = OA\Components::ref($typeSchema);
                            $property->type = Generator::UNDEFINED;
                        }
                    }
                }
            }
        }

        if (!Generator::isDefault($property->const) && Generator::isDefault($property->type)) {
            if (!$this->mapNativeType($property, gettype($property->const))) {
                $property->type = Generator::UNDEFINED;
            }
        }
    }

    protected function isNullable(string $typeDescription): bool
    {
        return in_array('null', explode('|', strtolower($typeDescription)));
    }

    protected function stripNull(string $typeDescription): string
    {
        if (strpos($typeDescription, '|') === false) {
            return $typeDescription;
        }
        $types = [];
        foreach (explode('|', $typeDescription) as $type) {
            if (strtolower($type) === 'null') {
                continue;
            }
            $types[] = $type;
        }

        return implode('|', $types);
    }

    protected function applyRef(Analysis $analysis, OA\Property $property, string $ref): void
    {
        if ($property->nullable === true) {
            $property->oneOf = [
                $schema = new OA\Schema([
                    'ref' => $ref,
                    '_context' => new Context(['generated' => true], $property->_context),
                ]),
            ];
            $analysis->addAnnotation($schema, $schema->_context);
        } else {
            $property->ref = $ref;
        }
    }
}
