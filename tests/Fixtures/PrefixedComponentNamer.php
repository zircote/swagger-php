<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\ComponentNamer\ShortName;
use OpenApi\Contracts\AttributeInterface;

class PrefixedComponentNamer extends ShortName
{
    public function name(AttributeInterface $component): ?string
    {
        $name = parent::name($component);

        return $name === null ? null : 'Api' . $name;
    }
}
