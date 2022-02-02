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
        string|object|null $ref = null,
        ?Schema $schema = null,
        ?array $examples = null,
        ?string $style = null,
        ?bool $explode = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'parameter' => $parameter ?? Generator::UNDEFINED,
                'name' => $name ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'in' => Generator::isDefault($this->in) ? $in : $this->in,
                'required' => !Generator::isDefault($this->required) ? $this->required : ($required ?? Generator::UNDEFINED),
                'ref' => $ref ?? Generator::UNDEFINED,
                'style' => $style ?? Generator::UNDEFINED,
                'explode' => $explode ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
