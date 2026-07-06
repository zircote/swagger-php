<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ServerVariable extends OA\ServerVariable
{
    /**
     * @param list<string|int|float|bool|\UnitEnum|null>|class-string|null $enum
     * @param array<string,mixed>|null                                     $x
     * @param list<Attachable>|null                                        $attachables
     */
    public function __construct(
        ?string $serverVariable = null,
        ?string $description = Undefined::UNDEFINED,
        ?string $default = null,
        array|string|null $enum = null,
        ?array $variables = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'serverVariable' => $serverVariable ?? Undefined::UNDEFINED,
                'description' => $description,
                'default' => $default ?? Undefined::UNDEFINED,
                'enum' => $enum ?? Undefined::UNDEFINED,
                'variables' => $variables ?? Undefined::UNDEFINED,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
            ]);
    }
}
