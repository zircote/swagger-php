<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ExpandClasses;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class BaseThatImplements implements BaseInterface
{
    /**
     * @OA\Property
     *
     * @var string
     */
    public $baseProperty;

    /**
     * @inheritDoc
     */
    public function getInterfaceProperty()
    {
        return 'foo';
    }
}
