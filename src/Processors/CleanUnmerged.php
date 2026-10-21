<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;

/**
 * @deprecated since 7.0, removed in 8.0 - write an augmenter instead, see `OpenApi\Builder::withAugmenters()`
 */
class CleanUnmerged
{
    public function __invoke(Analysis $analysis): void
    {
        $split = $analysis->split();
        $merged = $split->merged->annotations;
        $unmerged = $split->unmerged->annotations;

        /** @var OA\AbstractAnnotation $annotation */
        foreach ($analysis->annotations as $annotation) {
            if (property_exists($annotation, '_unmerged')) {
                $kept = [];
                foreach ($annotation->_unmerged as $item) {
                    if (!$merged->offsetExists($item)) {
                        $kept[] = $item; // drop the ones that were merged
                    }
                }
                $annotation->_unmerged = $kept;
            }
        }
        $analysis->openapi->_unmerged = [];
        foreach ($unmerged as $annotation) {
            $analysis->openapi->_unmerged[] = $annotation;
        }
    }
}
