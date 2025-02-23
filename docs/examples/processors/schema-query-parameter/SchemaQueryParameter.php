<?php

declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SchemaQueryParameterProcessor;

use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;

/**
 * Custom processor to translate the vendor tag `query-args-$ref` into query parameter annotations.
 *
 * Details for the parameters are taken from the referenced schema.
 */
class SchemaQueryParameter
{
    public const REF = 'query-args-$ref';

    public function __invoke(Analysis $analysis): void
    {
        /** @var Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(Operation::class);

        foreach ($operations as $operation) {
            if ($operation->x !== Generator::UNDEFINED && array_key_exists(self::REF, $operation->x)) {
                if (!is_string($operation->x[self::REF])) {
                    throw new \InvalidArgumentException('Value of `x.' . self::REF . '` must be a string');
                }

                $schema = $analysis->getSchemaForSource($operation->x[self::REF]);
                if (!$schema instanceof Schema) {
                    throw new \InvalidArgumentException('Value of `x.' . self::REF . "` contains reference to unknown schema: `{$operation->x[self::REF]}`");
                }

                $this->expandQueryArgs($operation, $schema);
                $this->cleanUp($operation);
            }
        }
    }

    /**
     * Expand the given operation by injecting parameters for all properties of the given schema.
     */
    private function expandQueryArgs(Operation $operation, Schema $schema): void
    {
        if (Generator::isDefault($schema->properties) || !$schema->properties) {
            return;
        }

        $operation->parameters = Generator::isDefault($operation->parameters) ? [] : $operation->parameters;

        foreach ($schema->properties as $property) {
            $isNullable = Generator::isDefault($property->nullable) ? false : $property->nullable;
            $schema = new Schema(
                type: Generator::isDefault($property->format) ? $property->type : $property->format,
                nullable: $isNullable
            );
            $schema->_context = $operation->_context; // inherit context from operation, required to pretend to be a parameter

            $parameter = new Parameter(
                name: $property->property,
                description: Generator::isDefault($property->description) ? null : $property->description,
                in: 'query',
                required: !$isNullable,
                schema: $schema,
                example: $property->example,
            );
            $parameter->_context = $operation->_context; // inherit context from operation, required to pretend to be a parameter

            $operation->parameters[] = $parameter;
        }
    }

    private function cleanUp(Operation $operation): void
    {
        unset($operation->x[self::REF]);
        if (!$operation->x) {
            $operation->x = Generator::UNDEFINED;
        }
    }
}
