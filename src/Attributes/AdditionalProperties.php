<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AdditionalProperties extends \OpenApi\Annotations\AdditionalProperties
{
    public function __construct(
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
