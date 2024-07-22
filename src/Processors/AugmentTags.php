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

        $usedTagNames = [];
        foreach ($operations as $operation) {
            if (!Generator::isDefault($operation->tags)) {
                $usedTagNames = array_merge($usedTagNames, $operation->tags);
            }
        }
        $usedTagNames = array_unique($usedTagNames);

        $declaredTags = [];
        if (!Generator::isDefault($analysis->openapi->tags)) {
            foreach ($analysis->openapi->tags as $tag) {
                $declaredTags[$tag->name] = $tag;
            }
        }

        if ($usedTagNames) {
            $declatedTagNames = array_keys($declaredTags);
            foreach ($usedTagNames as $tagName) {
                if (!in_array($tagName, $declatedTagNames)) {
                    $analysis->openapi->merge([new OA\Tag(['name' => $tagName, 'description' => $tagName])]);
                }
            }
        }

        foreach ($declaredTags as $tag) {
            if (!in_array($tag->name, $usedTagNames)) {
                if (false !== $index = array_search($tag, $analysis->openapi->tags, true)) {
                    $analysis->annotations->detach($tag);
                    unset($analysis->openapi->tags[$index]);
                    $analysis->openapi->tags = array_values($analysis->openapi->tags);
                }
            }
        }
    }
}
