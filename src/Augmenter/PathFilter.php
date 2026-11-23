<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;

/**
 * Filters operations by tag and/or path patterns.
 *
 * With `tags` or `paths` set, only operations matching one of them are kept. Operations matching
 * `excludeTags` or `excludePaths` are then removed. If no lists are set, no filtering is performed.
 * All filter expressions must be valid regular expressions (with delimiters).
 *
 * @implements PipeInterface<Specification>
 */
class PathFilter implements PipeInterface
{
    /**
     * @param list<string> $tags         Regular expressions to match operation tags
     * @param list<string> $paths        Regular expressions to match operation paths
     * @param list<string> $excludeTags  Regular expressions to match operation tags to remove
     * @param list<string> $excludePaths Regular expressions to match operation paths to remove
     */
    public function __construct(
        #[Config('A list of regular expressions to match <code>tags</code> to include.')]
        protected array $tags = [],
        #[Config('A list of regular expressions to match <code>paths</code> to include.')]
        protected array $paths = [],
        #[Config('A list of regular expressions to match <code>tags</code> to exclude, applied after the include lists.')]
        protected array $excludeTags = [],
        #[Config('A list of regular expressions to match <code>paths</code> to exclude, applied after the include lists.')]
        protected array $excludePaths = [],
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        if ($this->tags || $this->paths) {
            $payload->operations = array_values(array_filter(
                $payload->operations,
                fn (OA\Operation $operation): bool => $this->matches($operation, $this->tags, $this->paths),
            ));
        }

        if ($this->excludeTags || $this->excludePaths) {
            $payload->operations = array_values(array_filter(
                $payload->operations,
                fn (OA\Operation $operation): bool => !$this->matches($operation, $this->excludeTags, $this->excludePaths),
            ));
        }

        return null;
    }

    /**
     * @param list<string> $tags
     */
    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param list<string> $paths
     */
    public function setPaths(array $paths): static
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @param list<string> $excludeTags
     */
    public function setExcludeTags(array $excludeTags): static
    {
        $this->excludeTags = $excludeTags;

        return $this;
    }

    /**
     * @param list<string> $excludePaths
     */
    public function setExcludePaths(array $excludePaths): static
    {
        $this->excludePaths = $excludePaths;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Reduce;
    }

    /**
     * Whether one of the operation's tags matches one of the tag patterns, or its path one of the path patterns.
     *
     * @param list<string> $tags
     * @param list<string> $paths
     */
    protected function matches(OA\Operation $operation, array $tags, array $paths): bool
    {
        return $this->matchesAny($tags, $operation->tags ?? [])
            || $this->matchesAny($paths, $operation->path !== null ? [$operation->path] : []);
    }

    /**
     * Whether any of the patterns matches any of the values.
     *
     * @param list<string>  $patterns
     * @param array<string> $values
     */
    protected function matchesAny(array $patterns, array $values): bool
    {
        foreach ($patterns as $pattern) {
            foreach ($values as $value) {
                if (preg_match($pattern, $value)) {
                    return true;
                }
            }
        }

        return false;
    }
}
