<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MediaType extends \OpenApi\Annotations\MediaType
{
    /**
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $mediaType = null,
        ?Schema $schema = null,
        $example = null,
        ?array $examples = null,
        ?string $encoding = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'mediaType' => $mediaType ?? Generator::UNDEFINED,
                'example' => $example ?? Generator::UNDEFINED,
                'encoding' => $encoding ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
