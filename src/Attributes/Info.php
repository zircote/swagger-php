<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends \OpenApi\Annotations\Info
{
    public function __construct(
        string $version = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $title = Generator::UNDEFINED,
        string $termsOfService = Generator::UNDEFINED,
        ?Contact $contact = null,
        ?License $license = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'version' => $version,
                'description' => $description,
                'title' => $title,
                'termsOfService' => $termsOfService,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($contact, $license, $attachables),
            ]);
    }
}
