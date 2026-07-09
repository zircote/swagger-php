<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Augmenters enrich a Specification with data typically inferred from PHP source code.
 *
 * They run after assembly (attributes collected and nested) but before compilation,
 * filling in details that users would otherwise have to specify explicitly — such as
 * types from PHP type hints, descriptions from docblocks, or enum values from backed enums.
 *
 * Augmenters are invoked via the Pipeline, so they must be callable.
 */
interface AugmenterInterface
{
    public function __invoke(Specification $specification): void;
}
