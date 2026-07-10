<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\OpenApiException;
use OpenApi\PipeInterface;

/**
 * @template T
 */
class Pipeline
{
    /**
     * @var array<callable(T): (T|null)>
     */
    protected $pipes = [];

    /**
     * @var list<string>|null ordered group keys; null means no grouping (insertion order only)
     */
    protected ?array $groups = null;

    protected ?string $defaultGroup = null;

    /**
     * @param list<string|\BackedEnum>|null $groups       Ordered group names/enums. When set, process() executes pipes in group order.
     * @param string|\BackedEnum|null       $defaultGroup Group for pipes without PipeInterface. Must be in $groups if groups are set.
     */
    public function __construct(array $pipes = [], ?array $groups = null, string|\BackedEnum|null $defaultGroup = null)
    {
        $this->pipes = $pipes;

        if ($groups !== null) {
            $this->groups = array_map(self::groupKey(...), $groups);
            $defaultKey = $defaultGroup !== null ? self::groupKey($defaultGroup) : null;

            if ($defaultKey !== null && !in_array($defaultKey, $this->groups, true)) {
                throw new OpenApiException("Default group '{$defaultKey}' must be listed in groups");
            }
            $this->defaultGroup = $defaultKey ?? $this->groups[array_key_last($this->groups)];
        }
    }

    public function add(callable $pipe): Pipeline
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * @param callable|class-string|null $pipe
     */
    public function remove($pipe = null, ?callable $matcher = null): Pipeline
    {
        if (!$pipe && !$matcher) {
            throw new OpenApiException('pipe or callable must not be empty');
        }

        // allow matching on class name if $pipe in a string
        if (is_string($pipe) && !$matcher) {
            $pipeClass = $pipe;
            $matcher = (static fn ($pipe): bool => !$pipe instanceof $pipeClass);
        }

        if ($matcher) {
            $tmp = [];
            foreach ($this->pipes as $pipe) {
                if ($matcher($pipe)) {
                    $tmp[] = $pipe;
                }
            }

            $this->pipes = $tmp;
        } else {
            if (false === ($key = array_search($pipe, $this->pipes, true))) {
                return $this;
            }

            unset($this->pipes[$key]);

            $this->pipes = array_values($this->pipes);
        }

        return $this;
    }

    /**
     * @param callable|class-string $matcher used to determine the position to insert
     *                                       either an <code>int</code> from a callable or, in the case of <code>$matcher</code> being
     *                                       a <code>class-string</code>, the position before the first pipe of that class
     */
    public function insert(callable $pipe, $matcher): Pipeline
    {
        if (is_string($matcher)) {
            $before = $matcher;
            $matcher = static function (array $pipes) use ($before): int|string|null {
                foreach ($pipes as $ii => $current) {
                    if ($current instanceof $before) {
                        return $ii;
                    }
                }

                return null;
            };
        }

        $index = $matcher($this->pipes);
        if (null === $index || $index < 0 || $index > count($this->pipes)) {
            throw new OpenApiException('Matcher result out of range');
        }

        array_splice($this->pipes, $index, 0, [$pipe]);

        return $this;
    }

    public function walk(callable $walker): Pipeline
    {
        foreach ($this->pipes as $pipe) {
            $walker($pipe);
        }

        return $this;
    }

    /**
     * @param T $payload
     *
     * @return T
     */
    public function process(mixed $payload)
    {
        foreach ($this->ordered() as $pipe) {
            $payload = $pipe($payload) ?: $payload;
        }

        return $payload;
    }

    /**
     * Return pipes in execution order: grouped if groups are configured, otherwise insertion order.
     *
     * @return list<callable>
     */
    protected function ordered(): array
    {
        if ($this->groups === null) {
            return $this->pipes;
        }

        $buckets = array_fill_keys($this->groups, []);

        foreach ($this->pipes as $pipe) {
            $group = $pipe instanceof PipeInterface ? self::groupKey($pipe->group()) : $this->defaultGroup;
            if (!isset($buckets[$group])) {
                throw new OpenApiException("Pipe declares unknown group '{$group}'; valid groups: " . implode(', ', $this->groups));
            }
            $buckets[$group][] = $pipe;
        }

        return array_merge(...array_values($buckets));
    }

    protected static function groupKey(string|\BackedEnum $group): string
    {
        return $group instanceof \BackedEnum ? $group->value : $group;
    }
}
