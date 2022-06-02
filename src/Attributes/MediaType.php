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
     * @param array<string,Examples>   $examples
     * @param array<string,mixed>      $encoding
     * @param array<string,mixed>|null $x
     * @param Attachable[]|null        $attachables
     */
    public function __construct(
        ?string $mediaType = null,
        ?Schema $schema = null,
        $example = Generator::UNDEFINED,
        ?array $examples = null,
        ?array $encoding = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'mediaType' => $mediaType ?? Generator::UNDEFINED,
                'example' => $example,
                'encoding' => $encoding ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
