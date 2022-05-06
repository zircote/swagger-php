<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

trait ParameterTrait
{
    /**
     * @param array<string,Examples>    $examples
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = null,
        ?string $in = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        string|object|null $ref = null,
        ?Schema $schema = null,
        $example = Generator::UNDEFINED,
        ?array $examples = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?array $spaceDelimited = null,
        ?array $pipeDelimited = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'parameter' => $parameter ?? Generator::UNDEFINED,
                'name' => $name ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'in' => Generator::isDefault($this->in) ? $in : $this->in,
                'required' => $required ?? Generator::UNDEFINED,
                'deprecated' => $deprecated ?? Generator::UNDEFINED,
                'allowEmptyValue' => $allowEmptyValue ?? Generator::UNDEFINED,
                'ref' => $ref ?? Generator::UNDEFINED,
                'example' => $example,
                'style' => $style ?? Generator::UNDEFINED,
                'explode' => $explode ?? Generator::UNDEFINED,
                'allowReserved' => $allowReserved ?? Generator::UNDEFINED,
                'spaceDelimited' => $spaceDelimited ?? Generator::UNDEFINED,
                'pipeDelimited' => $pipeDelimited ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
