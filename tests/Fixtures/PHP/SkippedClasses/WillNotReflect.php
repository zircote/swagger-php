<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\SkippedClasses;

// skipped by the scan: the parent does not exist, so the class will not load
class WillNotReflect extends DeliberatelyAbsentParent
{
}
