<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Spec\Operation;
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

    public function validate(Specification $specification): array
    {
        $diagnostics = parent::validate($specification);

        $hasPaths = (bool) array_filter($specification->operations, fn (Operation $op): bool => $op->path !== null);
        $hasWebhooks = (bool) array_filter($specification->operations, fn (Operation $op): bool => $op->webhook !== null);
        $hasComponents = $specification->schemas || $specification->responses
            || $specification->parameters || $specification->requestBodies
            || $specification->headers || $specification->securitySchemes
            || $specification->links || $specification->examples;

        if (!$hasPaths && !$hasWebhooks && !$hasComponents) {
            // Already checked by parent, but 3.2 also requires pathItems as alternative
        }

        return $diagnostics;
    }

    public function compile(Specification $specification): array
    {
        $output = parent::compile($specification);
        $output['openapi'] = $specification->openapi->version ?? '3.2.0';

        return $output;
    }
}
