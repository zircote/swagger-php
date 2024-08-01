<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Look at all (direct) interfaces for a schema and:
 * - merge interfaces annotations/methods into the schema if the interface does not have a schema itself
 * - inherit from the interface if it has a schema (allOf).
 */
class ExpandInterfaces
{
    use Concerns\MergePropertiesTrait;

    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('class')) {
                $className = $schema->_context->fullyQualifiedName($schema->_context->class);
                $interfaces = $analysis->getInterfacesOfClass($className, true);

                if (class_exists($className) && ($parent = get_parent_class($className)) && ($inherited = array_keys(class_implements($parent)))) {
                    // strip interfaces we inherit from ancestor
                    foreach (array_keys($interfaces) as $interface) {
                        if (in_array(ltrim($interface, '\\'), $inherited)) {
                            unset($interfaces[$interface]);
                        }
                    }
                }

                $existing = [];
                foreach ($interfaces as $interface) {
                    $interfaceName = $interface['context']->fullyQualifiedName($interface['interface']);
                    $interfaceSchema = $analysis->getSchemaForSource($interfaceName);
                    if ($interfaceSchema) {
                        $refPath = Generator::isDefault($interfaceSchema->schema) ? $interface['interface'] : $interfaceSchema->schema;
                        $this->inheritFrom($analysis, $schema, $interfaceSchema, $refPath, $interface['context']);
                    } else {
                        $this->mergeMethods($schema, $interface, $existing);
                    }
                }
            }
        }
    }
}
