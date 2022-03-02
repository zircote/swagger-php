<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Property as AnnotationSchema;
use OpenApi\Attributes\Property as AttributeSchema;
use OpenApi\Generator;

/**
 * Transform const keywords to enum in OpenApi versions < 3.1.0.
 */
class BackportConstants
{
    public function __invoke(Analysis $analysis)
    {
        /** @var AnnotationSchema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([AnnotationSchema::class, AttributeSchema::class], true);

        foreach ($schemas as $schema) {
            if (Generator::isDefault($schema->const)) {
                continue;
            }

            if (version_compare($analysis->context->version, OpenApi::VERSION_3_1_0, '<')) {
                $schema->enum = [$schema->const];
                $schema->const = Generator::UNDEFINED;
            }
        }
    }
}
