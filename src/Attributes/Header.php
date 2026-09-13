<?php declare(strict_types=1);
/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Header extends OA\Header
{
    /**
     * @param string|class-string|object|null                         $ref
     * @param array<Examples>|null                                    $examples
     * @param array<MediaType>|JsonContent|XmlContent|Attachable|null $content
     * @param array<string,mixed>|null                                $x
     * @param list<Attachable>|null                                   $attachables
     */
    public function __construct(
        string|object|null $ref = null,
        ?string $header = null,
        ?string $description = Undefined::UNDEFINED,
        ?bool $required = null,
        ?Schema $schema = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        ?string $style = null,
        ?bool $explode = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        array|JsonContent|XmlContent|Attachable|null $content = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'ref' => $ref ?? Undefined::UNDEFINED,
            'header' => $header ?? Undefined::UNDEFINED,
            'description' => $description,
            'required' => $required ?? Undefined::UNDEFINED,
            'deprecated' => $deprecated ?? Undefined::UNDEFINED,
            'allowEmptyValue' => $allowEmptyValue ?? Undefined::UNDEFINED,
            'style' => $style ?? Undefined::UNDEFINED,
            'explode' => $explode ?? Undefined::UNDEFINED,
            'example' => $example,
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
            'value' => $this->combine($schema, $examples, $content),
        ]);
    }
}
