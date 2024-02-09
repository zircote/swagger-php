<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Generator;

trait GeneratorAwareTrait
{
    protected ?Generator $generator = null;

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }
}
