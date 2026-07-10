<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Ensures all tags used on operations exist in the global tags list.
 *
 * Adds missing Tag objects for any tag name referenced by operations.
 * Removes unused declared tags unless whitelisted.
 *
 * @implements PipeInterface<Specification>
 */
class Tag implements PipeInterface
{
    /** @var list<string> */
    protected array $whitelist = [];

    protected bool $withDescription = true;

    /**
     * @param list<string> $whitelist
     */
    public function __construct(array $whitelist = [], bool $withDescription = true)
    {
        $this->whitelist = $whitelist;
        $this->withDescription = $withDescription;
    }

    /**
     * Whitelist tags to keep even if not used. Use '*' to keep all.
     *
     * @param list<string> $whitelist
     */
    public function setWhitelist(array $whitelist): static
    {
        $this->whitelist = $whitelist;

        return $this;
    }

    /**
     * Enables/disables generation of default tag descriptions.
     */
    public function setWithDescription(bool $withDescription): static
    {
        $this->withDescription = $withDescription;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    public function __invoke(mixed $payload): mixed
    {
        $usedTagNames = $this->collectUsedTags($payload);

        $declaredTags = [];
        foreach ($payload->tags as $tag) {
            if ($tag->name !== null) {
                $declaredTags[$tag->name] = $tag;
            }
        }

        foreach ($usedTagNames as $tagName) {
            if (!isset($declaredTags[$tagName])) {
                $tag = new OA\Tag(
                    name: $tagName,
                    description: $this->withDescription ? $tagName : null,
                );
                $payload->tags[] = $tag;
                $declaredTags[$tagName] = $tag;
            }
        }

        $this->removeUnusedTags($usedTagNames, $declaredTags, $payload);

        return null;
    }

    /**
     * @return list<string>
     */
    protected function collectUsedTags(mixed $payload): array
    {
        $names = [];
        foreach ($payload->operations as $operation) {
            if ($operation->tags !== null) {
                array_push($names, ...$operation->tags);
            }
        }

        return array_values(array_unique($names));
    }

    protected function removeUnusedTags(array $usedTagNames, array $declaredTags, mixed $payload): void
    {
        if (in_array('*', $this->whitelist, true)) {
            return;
        }

        $tagsToKeep = array_merge($usedTagNames, $this->whitelist);

        $payload->tags = array_values(array_filter(
            $payload->tags,
            fn (OA\Tag $tag): bool => $tag->name === null || in_array($tag->name, $tagsToKeep, true),
        ));
    }
}
