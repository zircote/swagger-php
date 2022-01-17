<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;

/**
 * Update refs broken due to `allOf` augmenting.
 */
class AugmentRefs
{
    public function __invoke(Analysis $analysis)
    {
        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);

        // ref rewriting
        $updatedRefs = [];
        foreach ($schemas as $schema) {
            if ($schema->allOf!== Generator::UNDEFINED) {
                // do we have to keep track of properties refs that need updating?
                foreach ($schema->allOf as $ii => $allOfSchema) {
                    if ($allOfSchema->properties!== Generator::UNDEFINED) {
                        $updatedRefs[Components::ref($schema->schema . '/properties', false)] = Components::ref($schema->schema . '/allOf/0/properties', false);
                        break;
                    }
                }

                // rewriting is simpler if properties is first...
                usort($schema->allOf, function ($a, $b) {
                    return $a->properties!== Generator::UNDEFINED ? -1 : ($b->properties!== Generator::UNDEFINED ? 1 : 0);
                });
            }
        }

        if ($updatedRefs) {
            foreach ($analysis->annotations as $annotation) {
                if (property_exists($annotation, 'ref') && $annotation->ref !== Generator::UNDEFINED && $annotation->ref !== null) {
                    foreach ($updatedRefs as $origRef => $updatedRef) {
                        if (0 === strpos($annotation->ref, $origRef)) {
                            $annotation->ref = str_replace($origRef, $updatedRef, $annotation->ref);
                        }
                    }
                }
            }
        }
    }
}
