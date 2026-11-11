<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Compat;

use OpenApi\ComponentNamer\ShortName;
use OpenApi\Specification\ComponentKey;

/**
 * An override with the signatures its methods were released with in 7.5.0, which a subclass
 * written against 7.5.0 still has. Loading it fails if one of them changed.
 */
class LegacyNamer extends ShortName
{
    protected function qualifiers(ComponentKey $key): string
    {
        return parent::qualifiers($key);
    }
}
