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
     * @param array<MediaType>|JsonContent|XmlContent|null $content
     * @param array<string,string>|null                    $x
     * @param Attachable[]|null                            $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $request = null,
        ?string $description = null,
        ?bool $required = null,
        array|JsonContent|XmlContent|null $content = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Generator::UNDEFINED,
            'request' => $request ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'required' => $required ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($content, $attachables),
        ]);
    }
}
