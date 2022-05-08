<?php

namespace OpenApi\Processors;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema as AnnotationSchema;
use OpenApi\Attributes\Schema as AttributeSchema;

trait DocblockTrait
{
    /**
     * An annotation is a root if it is the top-level / outermost annotation in a PHP docblock.
     */
    public function isRoot(AbstractAnnotation $annotation): bool
    {
        if (!$annotation->_context) {
            return true;
        }

        if (1 == count($annotation->_context->annotations)) {
            return true;
        }

        // find best match
        $matchPriorityMap = [
            Operation::class => false,
            Property::class => false,
            Parameter::class => false,
            AnnotationSchema::class => true,
            AttributeSchema::class => true,
        ];
        foreach ($matchPriorityMap as $className => $strict) {
            foreach ($annotation->_context->annotations as $contextAnnotation) {
                if ($strict) {
                    if ($className == get_class($contextAnnotation)) {
                        return  $annotation === $contextAnnotation;
                    }
                } else {
                    if ($contextAnnotation instanceof $className) {
                        return  $annotation === $contextAnnotation;
                    }
                }
            }
        }

        return false;
    }
}
