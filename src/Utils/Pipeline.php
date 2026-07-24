<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\OpenApiException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @template T
 *
 * @extends TypedList<PipeInterface|callable>
 */
class Pipeline extends TypedList
{
    /**
     * @var list<string>|null ordered group keys; null means no grouping (insertion order only)
     */
    protected ?array $groups = null;

    protected ?string $defaultGroup = null;

    protected LoggerInterface $logger;

    /**
     * @param list<PipeInterface|callable>  $pipes
     * @param list<string|\BackedEnum>|null $groups       Ordered group names/enums. When set, process() executes pipes in group order.
     * @param string|\BackedEnum|null       $defaultGroup Group for pipes without PipeInterface. Must be in $groups if groups are set.
     */
    public function __construct(array $pipes = [], ?array $groups = null, string|\BackedEnum|null $defaultGroup = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($pipes);
        $this->logger = $logger ?? new NullLogger();

        if ($groups !== null) {
            if ($groups === []) {
                throw new OpenApiException('Groups must not be empty when provided');
            }

            $this->groups = array_map(self::groupKey(...), $groups);
            $defaultKey = $defaultGroup !== null
                ? self::groupKey($defaultGroup)
                : null;

            if ($defaultKey !== null && !in_array($defaultKey, $this->groups, true)) {
                throw new OpenApiException("Default group '{$defaultKey}' must be listed in groups");
            }
            $this->defaultGroup = $defaultKey ?? $this->groups[array_key_last($this->groups)];
        }
    }

    public function walk(callable $walker): static
    {
        foreach ($this->ordered() as $pipe) {
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
            if ($pipe instanceof LoggerAwareInterface) {
                $pipe->setLogger($this->logger);
            }
            $payload = $pipe($payload) ?? $payload;
        }

        return $payload;
    }

    public function configure(array $config): void
    {
        $config = $this->normaliseConfig($config);

        $walker = function (callable $pipe) use ($config): void {
            $rc = new \ReflectionClass($pipe);

            // apply config
            $pipeKey = lcfirst($rc->getShortName());
            if (array_key_exists($pipeKey, $config)) {
                foreach ($config[$pipeKey] as $name => $value) {
                    $setter = 'set' . ucfirst($name);
                    if (method_exists($pipe, $setter)) {
                        $pipe->{$setter}($value);
                    }
                }
            }
        };

        $this->walk($walker);
    }

    /**
     * Return pipes in execution order: grouped if groups are configured, otherwise insertion order.
     *
     * @return list<callable>
     */
    protected function ordered(): array
    {
        if ($this->groups === null) {
            return $this->items;
        }

        $buckets = array_fill_keys($this->groups, []);

        foreach ($this->items as $pipe) {
            $group = $pipe instanceof PipeInterface
                ? self::groupKey($pipe->group())
                : $this->defaultGroup;

            if (!isset($buckets[$group])) {
                throw new OpenApiException("Pipe declares unknown group '{$group}'; valid groups: " . implode(', ', $this->groups));
            }

            $buckets[$group][] = $pipe;
        }

        return array_merge(...array_values($buckets));
    }

    protected static function groupKey(string|\BackedEnum $group): string
    {
        return $group instanceof \BackedEnum
            ? (string) $group->value
            : $group;
    }

    protected function normaliseConfig(array $config): array
    {
        $normalised = [];
        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                $token = explode('=', (string) $value);
                if (2 === count($token)) {
                    // 'operationId.hash=false'
                    [$key, $value] = $token;
                }
            }

            if (in_array($value, ['true', 'false'])) {
                $value = 'true' == $value;
            }

            if ($isList = (str_ends_with((string) $key, '[]'))) {
                $key = substr((string) $key, 0, -2);
            }
            $token = explode('.', (string) $key);
            if (2 === count($token)) {
                // 'operationId.hash' => false
                // namespaced / pipe
                if ($isList) {
                    $normalised[$token[0]][$token[1]][] = $value;
                } else {
                    $normalised[$token[0]][$token[1]] = $value;
                }
            } else {
                if ($isList) {
                    $normalised[$key][] = $value;
                } else {
                    $normalised[$key] = $value;
                }
            }
        }

        return $normalised;
    }
}
