<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

/*
 * Discovery anchor only — this fixture is spec-mode material.
 *
 * `ScratchTest::scratchTestCases()` globs `Scratch/*.php` and skips `-spec` names, so a
 * classic file has to exist for `Spec32-spec.php` to be found at all. None of the fields
 * that fixture exercises exist in classic, and there is no classic or hybrid document, so
 * both modes skip and this file is never loaded.
 */
