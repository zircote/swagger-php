<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\TypeResolverInterface;

trait TypesTrait
{
    /**
     * @param string|array $type
     */
    public function mapNativeType(OA\Schema $schema, $type): bool
    {
        if (is_array($type)) {
            $mapped = [];
            foreach ($type as $t) {
                if (array_key_exists($t, TypeResolverInterface::$NATIVE_TYPE_MAP)) {
                    $t = TypeResolverInterface::$NATIVE_TYPE_MAP[$t];
                    if (is_array($t)) {
                        $t = $t[0];
                    }
                }
                $mapped[] = $t;
            }

            $schema->type = $mapped;

            return true;
        }

        if (!array_key_exists($type, TypeResolverInterface::$NATIVE_TYPE_MAP)) {
            return false;
        }

        $type = TypeResolverInterface::$NATIVE_TYPE_MAP[$type];
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
        $mapped = array_key_exists($type, TypeResolverInterface::$NATIVE_TYPE_MAP)
            ? TypeResolverInterface::$NATIVE_TYPE_MAP[$type]
            : $type;

        return is_array($mapped) ? $mapped[0] : $mapped;
    }
}
