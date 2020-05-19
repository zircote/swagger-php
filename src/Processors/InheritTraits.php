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
            if ($schema->_context->is('class')) {
                if ($traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($schema->_context->class))) {
                    if ($schema->allOf === UNDEFINED) {
                        $schema->allOf = [];
                    }
                }
                foreach ($traits as $trait) {
                    $schema->allOf[] = new Schema([
                        '_context' => $trait['context']->_context,
                        'ref' => Components::SCHEMA_REF . $trait['trait']
                    ]);
                }
            }
        }
    }
}
