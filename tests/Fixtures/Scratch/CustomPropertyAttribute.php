<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
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
        #[CustomOpenApiProperty()]
        public ?int $number,
    ) {
    }
}
