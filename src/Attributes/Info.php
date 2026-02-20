<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends OA\Info
{
    /**
     * @param array<string,mixed>|null $x
     * @param list<Attachable>|null    $attachables
     */
    public function __construct(
        ?string $version = null,
        ?string $description = Generator::UNDEFINED,
        ?string $title = null,
        ?string $termsOfService = null,
        ?Contact $contact = null,
        ?License $license = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'version' => $version ?? Generator::UNDEFINED,
                'description' => $description,
                'title' => $title ?? Generator::UNDEFINED,
                'termsOfService' => $termsOfService ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'attachables' => $attachables ?? Generator::UNDEFINED,
                'value' => $this->combine($contact, $license),
            ]);
    }
}
