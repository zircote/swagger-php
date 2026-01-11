<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

abstract class AbstractBaseClass
{
    #[OAT\Property(property: 'basefilter')]
    public string $filters;
}
