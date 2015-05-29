<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Path;
use Swagger\Logger;
use Swagger\Context;
use Swagger\Analysis;

/**
 * 
 */
class CleanUnmerged
{

    public function __invoke(Analysis $analysis)
    {
        $split = $analysis->split();
        $unmerged = $split->unmerged->annotations;
        $nested = new \SplObjectStorage();
        foreach ($split->merged->annotations as $annotation) {
            if (property_exists('_unmerged', $annotation)) {
                foreach ($annotation->_unmerged as $i => $item) {
                    if ($unmerged->contains($item) === false) {
                        unset($annotation->_unmerged[$i]); // Property was merged
                    } else {
                        $nested->attach($item);
                    }
                }
            }
        }
        $analysis->swagger->_unmerged = [];
        foreach ($unmerged as $annotation) {
            if ($nested->contains($annotation) === false) {
                $analysis->swagger->_unmerged[] = $annotation;
            }
        }
    }
}