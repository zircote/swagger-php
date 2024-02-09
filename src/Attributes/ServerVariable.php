<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ServerVariable extends \OpenApi\Annotations\ServerVariable
{
    /**
     * @param array<string|int|float|bool|\UnitEnum|null>|class-string|null $enum
     * @param array<string,mixed>|null                                      $x
     * @param Attachable[]|null                                             $attachables
     */
    public function __construct(
        ?string $serverVariable = null,
        ?string $description = null,
        ?string $default = null,
        array|string|null $enum = null,
        ?array $variables = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'serverVariable' => $serverVariable ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'default' => $default ?? Generator::UNDEFINED,
                'enum' => $enum ?? Generator::UNDEFINED,
                'variables' => $variables ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
