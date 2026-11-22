<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

/*
 * Discovery anchor only — this fixture is spec-mode material.
 *
 * `ScratchTest::scratchTestCases()` globs `Scratch/*.php` and skips `-spec` names, so a
 * classic file has to exist for `RequiredProperties-spec.php` to be found at all. Classic
 * infers no `required` list and has no per-property flag, so there is no classic or hybrid
 * document, both modes skip and this file is never loaded.
 */
