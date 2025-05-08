<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class AugmentDiscriminators
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Discriminator[] $discriminators */
        $discriminators = $analysis->getAnnotationsOfType(OA\Discriminator::class);

        foreach ($discriminators as $discriminator) {
            if (!Generator::isDefault($discriminator->mapping)) {
                foreach ($discriminator->mapping as $value => $type) {
                    if (is_string($type) && $typeSchema = $analysis->getSchemaForSource($type)) {
                        $discriminator->mapping[$value] = OA\Components::ref($typeSchema);
                    }
                }
            }
        }
    }
}
