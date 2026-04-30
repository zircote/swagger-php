<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

interface GeneratorAwareInterface
{
    /**
     * @return static
     */
    public function setGenerator(Generator $generator);
}
