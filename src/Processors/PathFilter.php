<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Processors\Concerns\AnnotationTrait;

/**
 * Allows to filter endpoints based on tags and/or path.
 *
 * If no `tags` or `paths` filters are set, no filtering is performed.
 *
 * All filter (regular) expressions must be enclosed within delimiter characters as they are used as-is.
 */
class PathFilter
{
    use AnnotationTrait;

    protected array $tags;

    protected array $paths;

    protected bool $recurseCleanup;

    public function __construct(array $tags = [], array $paths = [], bool $recurseCleanup = false)
    {
        $this->tags = $tags;
        $this->paths = $paths;
        $this->recurseCleanup = $recurseCleanup;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * A list of regular expressions to match <code>tags</code> to include.
     *
     * @param array<string> $tags
     */
    public function setTags(array $tags): PathFilter
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * A list of regular expressions to match <code>paths</code> to include.
     *
     * @param array<string> $paths
     */
    public function setPaths(array $paths): PathFilter
    {
        $this->paths = $paths;

        return $this;
    }

    public function isRecurseCleanup(): bool
    {
        return $this->recurseCleanup;
    }

    /**
     * Flag to do a recursive cleanup of unused paths and their nested annotations.
     */
    public function setRecurseCleanup(bool $recurseCleanup): void
    {
        $this->recurseCleanup = $recurseCleanup;
    }

    public function __invoke(Analysis $analysis)
    {
        if (($this->tags || $this->paths) && !Generator::isDefault($analysis->openapi->paths)) {
            $filtered = [];
            foreach ($analysis->openapi->paths as $pathItem) {
                $matched = null;
                foreach ($this->tags as $pattern) {
                    foreach ($pathItem->operations() as $operation) {
                        if (!Generator::isDefault($operation->tags)) {
                            foreach ($operation->tags as $tag) {
                                if (preg_match($pattern, $tag)) {
                                    $matched = $pathItem;
                                    break 3;
                                }
                            }
                        }
                    }
                }

                foreach ($this->paths as $pattern) {
                    if (preg_match($pattern, $pathItem->path)) {
                        $matched = $pathItem;
                        break;
                    }
                }

                if ($matched) {
                    $filtered[] = $matched;
                } else {
                    $this->removeAnnotation($analysis->annotations, $pathItem, $this->recurseCleanup);
                }
            }

            $analysis->openapi->paths = $filtered;
        }
    }
}
