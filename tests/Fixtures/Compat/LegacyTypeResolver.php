<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Compat;

use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeResolver;

/**
 * An override with the signatures its methods were released with in 7.5.0, which a subclass
 * written against 7.5.0 still has. Loading it fails if one of them changed.
 */
class LegacyTypeResolver extends TypeResolver
{
    public function resolve(\Reflector $reflector): ?SchemaType
    {
        return parent::resolve($reflector);
    }

    protected function doResolve(\Reflector $reflector): ?SchemaType
    {
        return parent::doResolve($reflector);
    }
}
