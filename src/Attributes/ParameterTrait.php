<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Undefined;

trait ParameterTrait
{
    /**
     * @param 'query'|'header'|'path'|'cookie'|null                   $in
     * @param string|class-string|object|null                         $ref
     * @param array<Examples>                                         $examples
     * @param array<MediaType>|JsonContent|XmlContent|Attachable|null $content
     * @param array<string,mixed>|null                                $x
     * @param list<Attachable>|null                                   $attachables
     */
    public function __construct(
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = Undefined::UNDEFINED,
        ?string $in = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        string|object|null $ref = null,
        ?Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        array|JsonContent|XmlContent|Attachable|null $content = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?array $spaceDelimited = null,
        ?array $pipeDelimited = null,
        mixed $deepObject = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'parameter' => $parameter ?? Undefined::UNDEFINED,
            'name' => $name ?? Undefined::UNDEFINED,
            'description' => $description,
            // next two are special as we override the default value for specific Parameter subclasses
            'in' => $in ?? (Undefined::isDefault($this->in) ? Undefined::UNDEFINED : $this->in),
            'required' => $required ?? (Undefined::isDefault($this->required) ? Undefined::UNDEFINED : $this->required),
            'deprecated' => $deprecated ?? Undefined::UNDEFINED,
            'allowEmptyValue' => $allowEmptyValue ?? Undefined::UNDEFINED,
            'ref' => $ref ?? Undefined::UNDEFINED,
            'example' => $example,
            'style' => $style ?? Undefined::UNDEFINED,
            'explode' => $explode ?? Undefined::UNDEFINED,
            'allowReserved' => $allowReserved ?? Undefined::UNDEFINED,
            'spaceDelimited' => $spaceDelimited ?? Undefined::UNDEFINED,
            'pipeDelimited' => $pipeDelimited ?? Undefined::UNDEFINED,
            'deepObject' => $deepObject ?? Undefined::UNDEFINED,
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
            'value' => $this->combine($schema, $examples, $content),
        ]);
    }
}
