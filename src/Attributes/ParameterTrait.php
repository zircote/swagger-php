<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

trait ParameterTrait
{
    public function __construct(
        array $properties = [],
        string $parameter = Generator::UNDEFINED,
        string $name = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $in = Generator::UNDEFINED,
        ?bool $required = null,
        string $ref = Generator::UNDEFINED,
        ?Schema $schema = null,
        ?array $examples = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct($properties + [
                'parameter' => $parameter,
                'name' => $name,
                'description' => $description,
                'in' => $this->in !== Generator::UNDEFINED ? $this->in : $in,
                'required' => $this->required !== Generator::UNDEFINED ? $this->required : ($required ?? Generator::UNDEFINED),
                'ref' => $ref,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($schema, $examples, $attachables),
            ]);
    }
}
