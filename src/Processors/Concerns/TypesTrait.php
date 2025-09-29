<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Tests\ComparingResolver;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;

trait TypesTrait
{
    public function mapNativeType(OA\Schema $schema, string $type): bool
    {
        $type = strtolower($type);
        if (!array_key_exists($type, TypeResolverInterface::NATIVE_TYPE_MAP)) {
            return false;
        }

        $type = TypeResolverInterface::NATIVE_TYPE_MAP[$type];
        if (is_array($type)) {
            if (Generator::isDefault($schema->format)) {
                $schema->format = $type[1];
            }
            $type = $type[0];
        }

        $schema->type = $type;

        return true;
    }

    public function native2spec(string $type): string
    {
        $mapped = array_key_exists($type, TypeResolverInterface::NATIVE_TYPE_MAP) ? TypeResolverInterface::NATIVE_TYPE_MAP[$type] : $type;

        return is_array($mapped) ? $mapped[0] : $mapped;
    }

    public function getTypeResolver(?Context $context = null): TypeResolverInterface
    {
        // return new ComparingResolver(new OpenApiTestCase(), $context);
        return class_exists(\Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver::class)
            ? new TypeInfoTypeResolver()
            : new LegacyTypeResolver($context);
    }
}
