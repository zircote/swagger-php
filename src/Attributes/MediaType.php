<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MediaType extends \OpenApi\Annotations\MediaType
{
    public function __construct(
        string $mediaType = Generator::UNDEFINED,
        ?Schema $schema = null,
        $example = Generator::UNDEFINED,
        ?array $examples = null,
        string $encoding = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'mediaType' => $mediaType,
                'example' => $example,
                'encoding' => $encoding,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
