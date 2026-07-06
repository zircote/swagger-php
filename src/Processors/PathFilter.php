<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Undefined;

/**
 * Allows to filter endpoints based on tags and/or path.
 *
 * If no <code>tags</code> or <code>paths</code> filters are set, no filtering is performed.
 *
 * All filter (regular) expressions must be enclosed within delimiter characters as they are used as-is.
 */
class PathFilter
{
    use Concerns\AnnotationTrait;

    protected array $tags;

    protected array $paths;

    public function __construct(array $tags = [], array $paths = [])
    {
        $this->tags = $tags;
        $this->paths = $paths;
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

    public function __invoke(Analysis $analysis): void
    {
        if (($this->tags || $this->paths) && !Undefined::isDefault($analysis->openapi->paths)) {
            $filtered = [];
            foreach ($analysis->openapi->paths as $pathItem) {
                $matched = null;
                foreach ($this->tags as $pattern) {
                    foreach ($pathItem->operations() as $operation) {
                        if (!Undefined::isDefault($operation->tags)) {
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
                    $this->removeAnnotationRecursive($analysis, $pathItem);
                }
            }

            $analysis->openapi->paths = $filtered;
        }
    }
}
