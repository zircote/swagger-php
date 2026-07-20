<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Specification;

/**
 * Compiles a Specification into an OpenAPI 3.2.x document array.
 *
 * 3.2 is a superset of 3.1 — adds Tag summary/parent/kind and PathItem query.
 */
class OpenApi32Compiler extends OpenApi31Compiler
{
    protected const VERSIONS = ['3.2.0'];

    public function getVersion(): string
    {
        return '3.2.0';
    }
}
