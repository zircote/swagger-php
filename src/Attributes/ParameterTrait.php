<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

trait ParameterTrait
{
    /**
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
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'parameter' => $parameter ?? Generator::UNDEFINED,
                'name' => $name ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'in' => $this->in !== Generator::UNDEFINED ? $this->in : $in,
                'required' => $this->required !== Generator::UNDEFINED ? $this->required : ($required ?? Generator::UNDEFINED),
                'ref' => $ref ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
