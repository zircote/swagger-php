<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

final class CustomOpenApiProperty extends OAT\Property
{
}

#[OAT\Info(title: 'Api', version: '1.0.0')]
#[OAT\Get(path: '/api')]
#[OAT\Response(response: 200, description: 'All good')]
class Controller
{

}

#[OAT\Schema]
class CustomPropertyAttribute
{
    public function __construct(
        #[OAT\Property()]
        public ?int $number,
    ) {
    }
}
