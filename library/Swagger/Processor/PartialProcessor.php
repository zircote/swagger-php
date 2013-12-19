<?php

namespace Swagger\Processor;

use Swagger\Logger;
use Swagger\Parser;

class PartialProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof \Swagger\Annotations\Partial;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        Logger::notice('Unexpected "' . $annotation->identity() . '", @SWG\Partial is a pointer to a partial and should inside another annotation in ' . Annotations\AbstractAnnotation::$context);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'zircote_partial';
    }
}
