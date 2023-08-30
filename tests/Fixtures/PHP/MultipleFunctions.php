<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Test", version="1.0")
 */
class MultipleFunctions
{
    public function first()
    {
        $category = new \stdClass();
        $prefix = '1';
        $category->name = '1';

        return isset($category->{'name' . $prefix}) && $category->{'name' . $prefix}
            ? $category->{'name' . $prefix}
            : $category->name;
    }

    public function second()
    {

    }
}
