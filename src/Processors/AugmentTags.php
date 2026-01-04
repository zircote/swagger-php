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
class AugmentTags
{
    /** @var array<string> */
    protected array $whitelist;

    protected bool $withDescription;

    public function __construct(array $whitelist = [], bool $withDescription = true)
    {
        $this->whitelist = $whitelist;
        $this->withDescription = $withDescription;
    }

    /**
     * Whitelist tags to keep even if not used. <code>*</code> may be used to keep all unused.
     */
    public function setWhitelist(array $whitelist): AugmentTags
    {
        $this->whitelist = $whitelist;

        return $this;
    }

    /**
     * Enables/disables generation of default tag descriptions.
     */
    public function setWithDescription(bool $withDescription): AugmentTags
    {
        $this->withDescription = $withDescription;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
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
        if ($declaredTags) {
            // last one wins
            $analysis->openapi->tags = array_values($declaredTags);
        }

        // Add a tag for each tag that is used in operations but not declared in the global tags
        if ($usedTagNames) {
            $declatedTagNames = array_keys($declaredTags);
            foreach ($usedTagNames as $tagName) {
                if (!in_array($tagName, $declatedTagNames)) {
                    $analysis->openapi->merge([new OA\Tag([
                        'name' => $tagName,
                        'description' => $this->withDescription
                            ? $tagName
                            : Generator::UNDEFINED,
                    ])]);
                }
            }
        }

        // clear invalid parents
        foreach ($declaredTags as $tag) {
            if (!array_key_exists($tag->parent, $declaredTags)) {
                $tag->parent = Generator::UNDEFINED;
            }
        }

        $this->removeUnusedTags($usedTagNames, $declaredTags, $analysis);
    }

    private function removeUnusedTags(array $usedTagNames, array $declaredTags, Analysis $analysis): void
    {
        if (in_array('*', $this->whitelist)) {
            return;
        }

        $tagsToKeep = array_merge($usedTagNames, $this->whitelist);
        foreach ($declaredTags as $tag) {
            if (!in_array($tag->name, $tagsToKeep)) {
                if (false !== $index = array_search($tag, $analysis->openapi->tags, true)) {
                    $analysis->annotations->offsetUnset($tag);
                    unset($analysis->openapi->tags[$index]);
                    $analysis->openapi->tags = array_values($analysis->openapi->tags);
                }
            }
        }
    }
}
