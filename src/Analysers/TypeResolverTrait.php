<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Context;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;

trait TypeResolverTrait
{
    public function getTypeResolver(?Context $context = null): TypeResolverInterface
    {
        return class_exists('Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver')
            ? new TypeInfoTypeResolver()
            : new LegacyTypeResolver($context);
    }
}
