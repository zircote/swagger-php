<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ServerVariable extends \OpenApi\Annotations\ServerVariable
{
    public function __construct(
        string $serverVariable = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $default = Generator::UNDEFINED,
        ?array $enum = null,
        ?array $variables = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'serverVariable' => $serverVariable,
                'description' => $description,
                'default' => $default,
                'enum' => $enum ?? Generator::UNDEFINED,
                'variables' => $variables ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
