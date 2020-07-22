<?php

namespace OpenApi\Processors;

use OpenApi\Annotations\Components;
use OpenApi\Annotations\Schema;
use OpenApi\Analysis;

class InheritTraits
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        foreach ($schemas as $schema) {
            if ($schema->_context->is('class') || $schema->_context->is('trait')) {
                $source = $schema->_context->class ?: $schema->_context->trait;
                $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($source), true);
                foreach ($traits as $trait) {
                    if ($analysis->getSchemaForSource($trait['context']->fullyQualifiedName($trait['trait']))) {
                        if ($schema->allOf === UNDEFINED) {
                            $schema->allOf = [];
                        }
                        $schema->allOf[] = new Schema([
                            '_context' => $trait['context']->_context,
                            'ref' => Components::SCHEMA_REF . $trait['trait']
                        ]);
                    }
                }
            }
        }
    }
}
