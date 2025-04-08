<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;
use OpenApi\Tests\Fixtures\PHP\Label;

#[Label(name: 'custom')]
#[OAT\Response()]
#[OAT\Header(header: 'X-Rate-Limit', allowEmptyValue: true)]
class UsingAttributes
{
}
