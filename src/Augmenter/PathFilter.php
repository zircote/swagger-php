<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Filters operations by tag and/or path patterns.
 *
 * If no tags or paths filters are set, no filtering is performed.
 * All filter expressions must be valid regular expressions (with delimiters).
 *
 * @implements PipeInterface<Specification>
 */
class PathFilter implements PipeInterface
{
    /**
     * @param list<string> $tags  Regular expressions to match operation tags
     * @param list<string> $paths Regular expressions to match operation paths
     */
    public function __construct(
        protected array $tags = [],
        protected array $paths = [],
    ) {
    }

    /**
     * A list of regular expressions to match <code>tags</code> to include.
     *
     * @param list<string> $tags
     */
    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * A list of regular expressions to match <code>paths</code> to include.
     *
     * @param list<string> $paths
     */
    public function setPaths(array $paths): static
    {
        $this->paths = $paths;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Reduce;
    }

    public function __invoke(mixed $payload): null
    {
        if (!$this->tags && !$this->paths) {
            return null;
        }

        $payload->operations = array_values(array_filter(
            $payload->operations,
            $this->matches(...),
        ));

        return null;
    }

    protected function matches(OA\Operation $operation): bool
    {
        return $this->matchesTags($operation) || $this->matchesPath($operation);
    }

    protected function matchesTags(OA\Operation $operation): bool
    {
        if (!$this->tags || !$operation->tags) {
            return false;
        }

        foreach ($this->tags as $pattern) {
            foreach ($operation->tags as $tag) {
                if (preg_match($pattern, $tag)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function matchesPath(OA\Operation $operation): bool
    {
        if (!$this->paths || $operation->path === null) {
            return false;
        }

        foreach ($this->paths as $pattern) {
            if (preg_match($pattern, $operation->path)) {
                return true;
            }
        }

        return false;
    }
}
