<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PathItem extends \OpenApi\Annotations\PathItem
{
    /**
     * @param string|class-string|object|null $ref
     * @param Server[]|null                   $servers
     * @param Parameter[]|null                $parameters
     * @param array<string,mixed>|null        $x
     * @param Attachable[]|null               $attachables
     */
    public function __construct(
        ?string $path = null,
        mixed $ref = null,
        ?string $summary = null,
        ?string $description = null,
        ?Get $get = null,
        ?Put $put = null,
        ?Post $post = null,
        ?Delete $delete = null,
        ?Options $options = null,
        ?Head $head = null,
        ?Patch $patch = null,
        ?Trace $trace = null,
        ?array $servers = null,
        ?array $parameters = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'path' => $path ?? Generator::UNDEFINED,
            'ref' => $ref ?? Generator::UNDEFINED,
            'summary' => $summary ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($get, $put, $post, $delete, $options, $head, $patch, $trace, $servers, $parameters, $attachables),
        ]);
    }
}
