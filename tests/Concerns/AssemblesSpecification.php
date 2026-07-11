<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Assembler;
use OpenApi\Specification;

trait AssemblesSpecification
{
    /**
     * @param class-string ...$classes
     */
    protected function assemble(string ...$classes): Specification
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler->getSpecification();
    }
}
