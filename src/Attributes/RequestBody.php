<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER)]
class RequestBody extends \OpenApi\Annotations\RequestBody
{
    public function __construct(
        string $description = Generator::UNDEFINED,
        ?bool $required = null,
        $content = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'description' => $description,
                'required' => $required ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($content, $attachables),
            ]);
    }
}
