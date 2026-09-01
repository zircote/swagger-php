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
 * that fixture exercises exist in classic, and there are no `Spec323.x.y-classic.yaml`
 * files, so every classic combination skips on `$spec === null` and this file is never
 * loaded.
 */
