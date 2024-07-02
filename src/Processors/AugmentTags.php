<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Ensures that all tags used on operations also exist in the global <code>tags</code> list.
 */
class AugmentTags implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);

        $usedTags = [];
        foreach ($operations as $operation) {
            if (!Generator::isDefault($operation->tags)) {
                $usedTags = array_merge($usedTags, $operation->tags);
            }
        }

        if ($usedTags) {
            $declaredTags = [];
            if (!Generator::isDefault($analysis->openapi->tags)) {
                foreach ($analysis->openapi->tags as $tag) {
                    $declaredTags[] = $tag->name;
                }
            }
            foreach ($usedTags as $tag) {
                if (!in_array($tag, $declaredTags)) {
                    $analysis->openapi->merge([new OA\Tag(['name' => $tag, 'description' => $tag])]);
                }
            }
        }
    }
}
