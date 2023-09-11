<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Webhook extends \OpenApi\Annotations\Webhook
{
    /**
     * @param string|null              $name
     * @param PathItem|null            $pathItem
     * @param array<string,mixed>|null $x
     * @param Attachable[]|null        $attachables
     */
    public function __construct(
        ?string $name = null,
        ?PathItem $pathItem = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'name' => $name ?? Generator::UNDEFINED,
            'pathItem' => $pathItem ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($name, $pathItem, $attachables),
        ]);
    }
}
