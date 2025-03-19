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

        // allow matching on class name in $pipe in a string
        if (is_string($pipe) && !$matcher) {
            $pipeClass = $pipe;
            $matcher = function ($pipe) use ($pipeClass) {
                return !$pipe instanceof $pipeClass;
            };
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
            $matcher = function (array $pipes) use ($before) {
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
     * @param mixed $payload
     *
     * @return mixed
     */
    public function process($payload)
    {
        foreach ($this->pipes as $pipe) {
            /** @deprecated null payload returned from pipe */
            $payload = $pipe($payload) ?: $payload;
        }

        return $payload;
    }
}
