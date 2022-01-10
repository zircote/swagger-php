<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response extends \OpenApi\Annotations\Response
{
    public function __construct(
        string|object|null $ref = null,
        $response = null,
        ?string $description = null,
        ?array $headers = null,
        $content = null,
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
                'value' => $this->combine($headers, $content, $links, $attachables),
            ]);
    }
}
