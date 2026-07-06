<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class RequestBody extends OA\RequestBody
{
    /**
     * @param string|class-string|object|null                                                          $ref
     * @param array<MediaType|JsonContent|XmlContent>|MediaType|JsonContent|XmlContent|Attachable|null $content
     * @param array<string,mixed>|null                                                                 $x
     * @param list<Attachable>|null                                                                    $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $request = null,
        ?string $description = Undefined::UNDEFINED,
        ?bool $required = null,
        array|MediaType|JsonContent|XmlContent|Attachable|null $content = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Undefined::UNDEFINED,
            'request' => $request ?? Undefined::UNDEFINED,
            'description' => $description,
            'required' => $required ?? Undefined::UNDEFINED,
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
            'value' => $this->combine($content),
        ]);
    }
}
