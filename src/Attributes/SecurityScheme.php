<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class SecurityScheme extends OA\SecurityScheme
{
    /**
     * @param string|class-string|object|null     $ref
     * @param string|non-empty-array<string>|null $type
     * @param list<Flow>                          $flows
     * @param array<string,mixed>|null            $x
     * @param list<Attachable>|null               $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $securityScheme = null,
        string|array|null $type = null,
        ?string $description = Undefined::UNDEFINED,
        ?string $name = null,
        ?string $in = null,
        ?string $bearerFormat = null,
        ?string $scheme = null,
        ?string $openIdConnectUrl = null,
        ?array $flows = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'ref' => $ref ?? Undefined::UNDEFINED,
                'securityScheme' => $securityScheme ?? Undefined::UNDEFINED,
                'type' => $type ?? Undefined::UNDEFINED,
                'description' => $description,
                'name' => $name ?? Undefined::UNDEFINED,
                'in' => $in ?? Undefined::UNDEFINED,
                'bearerFormat' => $bearerFormat ?? Undefined::UNDEFINED,
                'scheme' => $scheme ?? Undefined::UNDEFINED,
                'openIdConnectUrl' => $openIdConnectUrl ?? Undefined::UNDEFINED,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
                'value' => $this->combine($flows),
            ]);
    }
}
