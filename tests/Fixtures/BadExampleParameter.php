<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

#[OAT\QueryParameter(
    name: 'bad',
    example: 'not good',
    examples: [new OAT\Examples(example: 'first', summary: 'First example', value: 'one')]
)]
class BadExampleParameter
{
}
