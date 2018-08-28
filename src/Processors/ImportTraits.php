<?php

namespace OpenApi\Processors;

use OpenApi\Analyser;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\OpenApi;
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
                            $context = $annotation->_context;
                            if ($annotation instanceof Property) {
                                // Use the property names for @OA\Property()
                                if ($annotation->property === UNDEFINED) {
                                    $annotation->property = $context->property;
                                }
                                if (in_array($annotation->property, $existing) === false) {
                                    $existing[] = $annotation->property;
                                    $schema->merge([$annotation], true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
