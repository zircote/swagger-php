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
        $response = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        $content = Generator::UNDEFINED,
        ?array $links = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'response' => $response,
                'description' => $description,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($content, $links, $attachables),
            ]);
    }
}
