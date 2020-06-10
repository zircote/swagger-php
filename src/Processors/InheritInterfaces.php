<?php

namespace OpenApi\Processors;

use OpenApi\Annotations\Components;
use OpenApi\Annotations\Schema;
use OpenApi\Analysis;

class InheritInterfaces
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        foreach ($schemas as $schema) {
            if ($schema->_context->is('class')) {
                if ($interfaces = $analysis->getInterfacesOfClass($schema->_context->fullyQualifiedName($schema->_context->class), true)) {
                    if ($schema->allOf === UNDEFINED) {
                        $schema->allOf = [];
                    }
                }
                foreach ($interfaces as $interface) {
                    $schema->allOf[] = new Schema([
                        '_context' => $interface['context']->_context,
                        'ref' => Components::SCHEMA_REF . $interface['interface']
                    ]);
                }
            }
        }
    }
}
