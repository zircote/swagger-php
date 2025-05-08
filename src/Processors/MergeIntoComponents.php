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
 * Merge reusable annotation into <code>@OA\Schemas</code>.
 */
class MergeIntoComponents
{
    public function __invoke(Analysis $analysis)
    {
        $components = $analysis->openapi->components;
        if (Generator::isDefault($components)) {
            $components = new OA\Components(['_context' => new Context(['generated' => true], $analysis->context)]);
        }

        /** @var OA\AbstractAnnotation $annotation */
        foreach ($analysis->annotations as $annotation) {
            if ($annotation instanceof OA\AbstractAnnotation
                && in_array(OA\Components::class, $annotation::$_parents)
                && false === $annotation->_context->is('nested')) {
                // A top level annotation.
                $components->merge([$annotation], true);
                $analysis->openapi->components = $components;
            }
        }
    }
}
