<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Context;
use OpenApi\Generator;

class AttributeAnnotationFactory implements AnnotationFactoryInterface
{
    public function build(\Reflector $reflector, Context $context): array
    {
        if ($context->is('annotations') === false) {
            $context->annotations = [];
        }

        $context->comment = $reflector->getDocComment() ?: Generator::UNDEFINED;

        // no proper way to inject
        Generator::$context = $context;
        $annotations = [];
        try {
            foreach ($reflector->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof AbstractAnnotation) {
                    $instance->_context = $context;
                }
                $annotations[] = $instance;
            }
        } finally {
            Generator::$context = null;
        }

        return $context->annotations = array_filter($annotations, function ($a) {
            return $a !== null;
        });
    }
}
