<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

// NOTE: this file uses "\r\n" linebreaks on purpose

namespace OpenApi\Tests\Fixtures;

use OpenApi\Attributes as OAT;

class RequestUsingAttribute
{
    #[OAT\Get(
        path: '/get/{id}',
        summary: 'get desc'
    )]
    public function get(#[OAT\PathParameter] int $id)
    {

    }
}
