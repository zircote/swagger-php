<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Contact extends \OpenApi\Annotations\Contact
{
    public function __construct(
        string $name = Generator::UNDEFINED,
        string $url = Generator::UNDEFINED,
        string $email = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name,
                'url' => $url,
                'email' => $email,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
