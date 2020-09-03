<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Schema;
use OpenApi\Util;

class InheritInterfaces
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        foreach ($schemas as $schema) {
            if ($schema->_context->is('class')) {
                $interfaces = $analysis->getInterfacesOfClass($schema->_context->fullyQualifiedName($schema->_context->class), true);
                foreach ($interfaces as $interface) {
                    $inferfaceSchema = $analysis->getSchemaForSource($interface['context']->fullyQualifiedName($interface['interface']));
                    $refPath = $inferfaceSchema->schema !== UNDEFINED ? $inferfaceSchema->schema : $interface['interface'];
                    if ($inferfaceSchema) {
                        if ($schema->allOf === UNDEFINED) {
                            $schema->allOf = [];
                        }
                        $schema->allOf[] = new Schema([
                            '_context' => $interface['context']->_context,
                            'ref' => Components::SCHEMA_REF.Util::refEncode($refPath),
                        ]);
                    }
                }
            }
        }
    }
}
