<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

trait GeneratorAwareTrait
{
    protected ?Generator $generator = null;

    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;

        return $this;
    }
}
