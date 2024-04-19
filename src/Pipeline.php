<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

class Pipeline
{
    /**
     * @var array<callable>
     */
    protected $pipes = [];

    public function __construct(array $pipes = [])
    {
        $this->pipes = $pipes;
    }

    /**
     * @deprecated This will be removed in 5.0
     */
    public function pipes(): array
    {
        return $this->pipes;
    }

    public function add(callable $pipe): Pipeline
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    public function remove(?callable $pipe, ?callable $matcher = null): Pipeline
    {
        if (!$pipe && !$matcher) {
            throw new \InvalidArgumentException('pipe or callable must not be empty');
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
     * @param callable $matcher Callable to determine the position to insert (returned as `int`)
     */
    public function insert(callable $pipe, callable $matcher): Pipeline
    {
        $index = $matcher($this->pipes);
        if (null === $index || $index < 0 || $index > count($this->pipes)) {
            throw new \InvalidArgumentException('Matcher result out of range');
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

    public function process($payload)
    {
        foreach ($this->pipes as $pipe) {
            /** @deprecated null payload returned from pipe */
            $payload = $pipe($payload) ?: $payload;
        }

        return $payload;
    }
}
