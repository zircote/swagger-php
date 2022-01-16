<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER)]
class RequestBody extends \OpenApi\Annotations\RequestBody
{
    /**
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $description = null,
        ?bool $required = null,
        $content = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'description' => $description ?? Generator::UNDEFINED,
                'required' => $required ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($content, $attachables),
            ]);
    }
}
