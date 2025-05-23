<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response extends OA\Response
{
    /**
     * @param string|class-string|object|null                                                                $ref
     * @param Header[]                                                                                       $headers
     * @param MediaType|JsonContent|XmlContent|Attachable|array<MediaType|JsonContent|XmlContent|Attachable> $content
     * @param Link[]                                                                                         $links
     * @param array<string,mixed>|null                                                                       $x
     * @param Attachable[]|null                                                                              $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        int|string|null $response = null,
        ?string $description = null,
        ?array $headers = null,
        MediaType|JsonContent|XmlContent|Attachable|array|null $content = null,
        ?array $links = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Generator::UNDEFINED,
            'response' => $response ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($headers, $content, $links),
        ]);
    }
}
