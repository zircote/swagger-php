<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;

/**
 * Clean up any remaining unmerged annotations.
 */
class CleanUnmerged
{
    public function __invoke(Analysis $analysis)
    {
        $split = $analysis->split();
        $merged = $split->merged->annotations;
        $unmerged = $split->unmerged->annotations;

        /** @var OA\AbstractAnnotation $annotation */
        foreach ($analysis->annotations as $annotation) {
            if (property_exists($annotation, '_unmerged')) {
                foreach ($annotation->_unmerged as $i => $item) {
                    if ($merged->contains($item) || $item instanceof OA\Attachable) {
                        unset($annotation->_unmerged[$i]);
                    }
                }
            }
        }
        $analysis->openapi->_unmerged = [];
        foreach ($unmerged as $annotation) {
            if (!$annotation instanceof OA\Attachable) {
                $analysis->openapi->_unmerged[] = $annotation;
            }
        }
    }
}
