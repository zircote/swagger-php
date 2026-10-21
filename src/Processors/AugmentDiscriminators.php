<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Undefined;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 *
 * @deprecated since 7.0, removed in 8.0 - write an augmenter instead, see `OpenApi\Builder::withAugmenters()`
 */
class AugmentDiscriminators
{
    public function __invoke(Analysis $analysis): void
    {
        $discriminators = $analysis->getAnnotationsOfType(OA\Discriminator::class);

        foreach ($discriminators as $discriminator) {
            if (!Undefined::isDefault($discriminator->mapping)) {
                foreach ($discriminator->mapping as $value => $type) {
                    if (is_string($type) && $typeSchema = $analysis->getAnnotationForSource($type)) {
                        $discriminator->mapping[$value] = OA\Components::ref($typeSchema);
                    }
                }
            }
        }
    }
}
