<?php

namespace Swagger\Processor;

use Swagger\Logger;
use Swagger\Parser;
use Swagger\Annotations\AbstractAnnotation;

class PartialIdProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof AbstractAnnotation && $annotation->hasPartialId();
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        $id = $annotation->_partialId;

        if ($parser->hasPartial($id)) {
            Logger::notice('partial="' . $annotation->_partialId . '" is not unique. another was found in ' . Annotations\AbstractAnnotation::$context);
        }

        $parser->setPartial($id, $annotation);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'zircote_partial_id';
    }
}
