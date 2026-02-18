<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PathItem extends OA\PathItem
{
    /**
     * @param string|class-string|object|null $ref
     * @param list<Server>|null               $servers
     * @param list<Parameter>|null            $parameters
     * @param array<string,mixed>|null        $x
     * @param list<Attachable>|null           $attachables
     */
    public function __construct(
        ?string $path = null,
        string|object|null $ref = null,
        ?string $summary = Generator::UNDEFINED,
        ?string $description = Generator::UNDEFINED,
        ?Get $get = null,
        ?Put $put = null,
        ?Post $post = null,
        ?Delete $delete = null,
        ?Options $options = null,
        ?Head $head = null,
        ?Patch $patch = null,
        ?Trace $trace = null,
        ?Query $query = null,
        ?array $servers = null,
        ?array $parameters = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'path' => $path ?? Generator::UNDEFINED,
            'ref' => $ref ?? Generator::UNDEFINED,
            'summary' => $summary,
            'description' => $description,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
            'value' => $this->combine($get, $put, $post, $delete, $options, $head, $patch, $trace, $query, $servers, $parameters),
        ]);
    }
}
