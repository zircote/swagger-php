<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class AugmentProperties implements GeneratorAwareInterface
{
    use Concerns\DocblockTrait;
    use Concerns\RefTrait;
    use GeneratorAwareTrait;

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Property[] $properties */
        $properties = $analysis->getAnnotationsOfType(OA\Property::class);

        foreach ($properties as $property) {
            $context = $property->_context;
            $reflector = $context->reflector;

            if (Generator::isDefault($property->property)) {
                $property->property = $property->_context->property;
            }

            if ($property->encoding instanceof OA\Encoding) {
                $property->encoding->property = $property->property;
            }

            if (Generator::isDefault($property->const) && $reflector instanceof \ReflectionClassConstant) {
                $property->const = $reflector->getValue();
            }

            if (Generator::isDefault($property->description)) {
                $typeAndDescription = $this->parseVarLine((string) $context->comment);

                if ($typeAndDescription['description']) {
                    $property->description = trim($typeAndDescription['description']);
                } elseif ($this->isDocblockRoot($property)) {
                    $property->description = $this->parseDocblock($context->comment);
                }
            } elseif (null === $property->description) {
                $property->description = Generator::UNDEFINED;
            }

            if (!Generator::isDefault($property->ref)) {
                continue;
            }

            if (Generator::isDefault($property->type)) {
                $this->generator->getTypeResolver()->augmentSchemaType($analysis, $property);
            }

            $this->generator->getTypeResolver()->mapNativeType($property, $property->type);

            if (Generator::isDefault($property->example) && ($example = $this->extractExampleDescription((string) $context->comment))) {
                $property->example = $example;
            }

            if (Generator::isDefault($property->deprecated) && ($deprecated = $this->isDeprecated($context->comment))) {
                $property->deprecated = $deprecated;
            }
        }
    }
}
