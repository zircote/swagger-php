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
class AugmentProperties
{
    use Concerns\DocblockTrait;
    use Concerns\RefTrait;
    use Concerns\TypesTrait;

    public function __invoke(Analysis $analysis): void
    {
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

            $typeAndDescription = $this->parseVarLine((string) $context->comment);

            if (Generator::isDefault($property->type)) {
                $this->augmentSchemaType($analysis, $property, $context, $typeAndDescription['type']);
            } else {
                if (!is_array($property->type)) {
                    $this->mapNativeType($property, $property->type);
                }
            }

            if (Generator::isDefault($property->description) && $typeAndDescription['description']) {
                $property->description = trim($typeAndDescription['description']);
            }
            if (Generator::isDefault($property->description) && $this->isDocblockRoot($property)) {
                $property->description = $this->parseDocblock($context->comment);
            }

            if (Generator::isDefault($property->example) && ($example = $this->extractExampleDescription((string) $context->comment))) {
                $property->example = $example;
            }

            if (Generator::isDefault($property->deprecated) && ($deprecated = $this->isDeprecated($context->comment))) {
                $property->deprecated = $deprecated;
            }
        }
    }

    protected function augmentSchemaType(Analysis $analysis, OA\Schema $schema, Context $context, ?string $varType): void
    {
        // docblock typehints
        if ($varType) {
            $allTypes = trim($varType);

            if ($this->isNullable($allTypes) && Generator::isDefault($schema->nullable)) {
                $schema->nullable = true;
            }

            $allTypes = $this->stripNull($allTypes);
            preg_match('/^([^\[\<]+)(.*$)/', $allTypes, $typeMatches);
            $type = $typeMatches[1];

            // finalise property type/ref
            if (!$this->mapNativeType($schema, $type) && Generator::isDefault($schema->items)) {
                $typeSchema = $analysis->getSchemaForSource($context->fullyQualifiedName($type));
                if (Generator::isDefault($schema->ref) && $typeSchema) {
                    $schema->ref = OA\Components::ref($typeSchema);
                }
            }

            // ok, so we possibly have a type or ref
            if (!Generator::isDefault($schema->ref) && $typeMatches[2] === '' && !Generator::isDefault($schema->nullable) && $schema->nullable) {
                $typeSchema = $analysis->getSchemaForSource($context->fullyQualifiedName($type));
                if ($typeSchema) {
                    $schema->ref = OA\Components::ref($typeSchema);
                }
            } elseif ($typeMatches[2] === '[]') {
                if (Generator::isDefault($schema->items)) {
                    $schema->items = new OA\Items(
                        [
                            'type' => $schema->type,
                            '_context' => new Context(['generated' => true], $context),
                        ]
                    );
                    $analysis->addAnnotation($schema->items, $schema->items->_context);
                    if (!Generator::isDefault($schema->ref)) {
                        $schema->items->ref = $schema->ref;
                        $schema->ref = Generator::UNDEFINED;
                    }
                    $schema->type = 'array';
                }
            } elseif ($schema->type === 'integer' && str_starts_with($typeMatches[2], '<') && str_ends_with($typeMatches[2], '>')) {
                [$min, $max] = explode(',', substr($typeMatches[2], 1, -1));

                if (is_numeric($min)) {
                    $schema->minimum = (int) $min;
                }
                if (is_numeric($max)) {
                    $schema->maximum = (int) $max;
                }
            } elseif ($type === 'positive-int') {
                $schema->type = 'integer';
                $schema->minimum = 1;
            } elseif ($type === 'negative-int') {
                $schema->type = 'integer';
                $schema->maximum = -1;
            } elseif ($type === 'non-positive-int') {
                $schema->type = 'integer';
                $schema->maximum = 0;
            } elseif ($type === 'non-negative-int') {
                $schema->type = 'integer';
                $schema->minimum = 0;
            } elseif ($type === 'non-zero-int') {
                $schema->type = 'integer';
                $schema->not = $schema->_context->isVersion('3.1.x') ? ['const' => 0] : ['enum' => [0]];
            }
        }

        // native typehints
        if ($context->type && !Generator::isDefault($context->type)) {
            if ($context->nullable === true && Generator::isDefault($schema->nullable)) {
                $schema->nullable = true;
            }
            $type = strtolower($context->type);
            if (!$this->mapNativeType($schema, $type)) {
                $typeSchema = $analysis->getSchemaForSource($context->fullyQualifiedName($type));
                if (Generator::isDefault($schema->ref) && $typeSchema) {
                    $this->applyRef($analysis, $schema, OA\Components::ref($typeSchema));
                } else {
                    if (is_string($context->type) && $typeSchema = $analysis->getSchemaForSource($context->type)) {
                        if (Generator::isDefault($schema->format)) {
                            $schema->ref = OA\Components::ref($typeSchema);
                            $schema->type = Generator::UNDEFINED;
                        }
                    }
                }
            }
        }

        if (!Generator::isDefault($schema->const) && Generator::isDefault($schema->type)) {
            if (!$this->mapNativeType($schema, gettype($schema->const))) {
                $schema->type = Generator::UNDEFINED;
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

    protected function applyRef(Analysis $analysis, OA\Schema $schema, string $ref): void
    {
        if ($schema->nullable === true) {
            $schema->oneOf = [
                $nullableSchema = new OA\Schema([
                    'ref' => $ref,
                    '_context' => new Context(['generated' => true], $schema->_context),
                ]),
            ];
            $analysis->addAnnotation($nullableSchema, $nullableSchema->_context);
        } else {
            $schema->ref = $ref;
        }
    }
}
