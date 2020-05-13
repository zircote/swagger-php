<?php

namespace OpenApi\Processors;

use OpenApi\Annotations\Property;
use OpenApi\Annotations\Definition;
use OpenApi\Annotations\Schema;
use OpenApi\Analysis;
use Traversable;

class ImportTraits
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        foreach ($schemas as $schema) {
            $existing = [];
            if ($schema->_context->is('class')) {
                $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($schema->_context->class));
                foreach ($traits as $trait) {
                    foreach ($trait['properties'] as $property) {
                        if (is_array($property->annotations) === false && !($property->annotations instanceof Traversable)) {
                            continue;
                        }
                        foreach ($property->annotations as $annotation) {
                            if ($annotation instanceof Property && in_array($annotation->_context->property, $existing) === false) {
                                $existing[] = $annotation->_context->property;
                                $schema->merge([$annotation], true);
                            }
                        }
                    }
                }
            }
        }
    }
}
