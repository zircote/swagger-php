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
                $this->augmentSchemaType($analysis, $property);
            }

            if (!is_array($property->type)) {
                $this->mapNativeType($property, $property->type);
            }

            if (Generator::isDefault($property->description)) {
                if ($typeAndDescription['description']) {
                    $property->description = $typeAndDescription['description'];
                } elseif ($this->isDocblockRoot($property)) {
                    $property->description = $this->parseDocblock($context->comment);
                }
            }

            if (Generator::isDefault($property->example) && ($example = $this->extractExampleDescription((string) $context->comment))) {
                $property->example = $example;
            }

            if (Generator::isDefault($property->deprecated) && ($deprecated = $this->isDeprecated($context->comment))) {
                $property->deprecated = $deprecated;
            }
        }
    }

    protected function augmentSchemaType(Analysis $analysis, OA\Schema $schema): void
    {
        $context = $schema->_context;
        $typeResolver = $this->getTypeResolver($context);

        $docblockDetails = $typeResolver->getDocblockTypeDetails($context->reflector);
        $reflectionTypeDetails = $typeResolver->getReflectionTypeDetails($context->reflector);

        $type2ref = function (OA\Schema $schema) use ($analysis) {
            if (!Generator::isDefault($schema->type)) {
                if ($typeSchema = $analysis->getSchemaForSource($schema->type)) {
                    $schema->type = Generator::UNDEFINED;
                    $schema->ref = OA\Components::ref($typeSchema);
                }
            }
        };

        // we only consider nullable hints if the type is explicitly set
        if (Generator::isDefault($schema->nullable)
            && (($docblockDetails->explicitType && $docblockDetails->nullable)
                || ($reflectionTypeDetails->explicitType && $reflectionTypeDetails->nullable))
        ) {
            $schema->nullable = true;
        }

        if (Generator::isDefault($schema->type) && ($docblockDetails->explicitType || $reflectionTypeDetails->explicitType)) {
            $details = $docblockDetails->explicitType && $docblockDetails->isArray
                // for arrays, we prefer the docblock type
                ? $docblockDetails
                // otherwise, use the reflection type if possible
                : ($reflectionTypeDetails->explicitType ? $reflectionTypeDetails : $docblockDetails);

            // for now
            $schema->type = $details->types[0];

            if ('int' === $schema->type && is_array($details->explicitDetails)) {
                if (array_key_exists('from', $details->explicitDetails)) {
                    $schema->minimum = $details->explicitDetails['from'];
                    $schema->maximum = $details->explicitDetails['to'];
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
}
