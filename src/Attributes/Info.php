<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends OA\Info
{
    /**
     * @param array<string,mixed>|null $x
     * @param list<Attachable>|null    $attachables
     */
    public function __construct(
        ?string $version = null,
        ?string $description = Undefined::UNDEFINED,
        ?string $title = null,
        ?string $termsOfService = null,
        ?Contact $contact = null,
        ?License $license = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'version' => $version ?? Undefined::UNDEFINED,
                'description' => $description,
                'title' => $title ?? Undefined::UNDEFINED,
                'termsOfService' => $termsOfService ?? Undefined::UNDEFINED,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
                'value' => $this->combine($contact, $license),
            ]);
    }
}
