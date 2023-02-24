<?php declare(strict_types=1);
/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Header extends \OpenApi\Annotations\Header
{
    /**
     * @param string|class-string|object|null $ref
     * @param array<string,mixed>|null        $x
     * @param Attachable[]|null               $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $header = null,
        ?string $description = null,
        ?bool $required = null,
        ?Schema $schema = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        // annotation4
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Generator::UNDEFINED,
            'header' => $header ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'required' => $required ?? Generator::UNDEFINED,
            'deprecated' => $deprecated ?? Generator::UNDEFINED,
            'allowEmptyValue' => $allowEmptyValue ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'value' => $this->combine($attachables, $schema),
        ]);
    }
}
