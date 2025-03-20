<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Contact extends OA\Contact
{
    /**
     * @param array<string,mixed>|null $x
     * @param Attachable[]|null        $attachables
     */
    public function __construct(
        ?string $name = null,
        ?string $url = null,
        ?string $email = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'name' => $name ?? Generator::UNDEFINED,
            'url' => $url ?? Generator::UNDEFINED,
            'email' => $email ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
        ]);
    }
}
