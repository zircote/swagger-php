<?php declare(strict_types=1);

namespace OpenApi\Examples\MiscPhp81;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(schema: 'MyList')]
class MyList
{
    public function __construct(
        #[Property]
        public Settings $settings,
        #[Property(items: new Items())]
        /** @var Bar[] */
        public array $bars,
    )
    {
    }
}
