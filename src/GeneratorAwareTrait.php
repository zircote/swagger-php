<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * @deprecated since 7.0, removed in 8.0
 */
trait GeneratorAwareTrait
{
    protected ?Generator $generator = null;

    public function setGenerator(Generator $generator): static
    {
        $this->generator = $generator;

        return $this;
    }
}
