<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;
use OpenApi\Annotations as OA;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends OA\Info
{
    /**
     * @param array<string,mixed>|null $x
     * @param Attachable[]|null        $attachables
     */
    public function __construct(
        ?string $version = null,
        ?string $description = null,
        ?string $title = null,
        ?string $termsOfService = null,
        ?Contact $contact = null,
        ?License $license = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'version' => $version ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'title' => $title ?? Generator::UNDEFINED,
                'termsOfService' => $termsOfService ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($contact, $license, $attachables),
            ]);
    }
}
