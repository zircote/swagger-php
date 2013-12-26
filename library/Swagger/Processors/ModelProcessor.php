<?php

namespace Swagger\Processors;

use Swagger\Parser;
use Swagger\Contexts\ClassContext;

/**
 * ModelProcessor
 *
 * @uses ProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ModelProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof \Swagger\Annotations\Model;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        if (!$annotation->hasPartialId()) {
            $parser->appendModel($annotation);
        }

        if ($context instanceof ClassContext) {
            $annotation->phpClass = $context->getClass();
            if ($annotation->id === null) {
                $annotation->id = basename(str_replace('\\', '/', $context->getClass()));
            }

            if ($annotation->description === null) {
                $annotation->description = $context->extractDescription();
            }
            $annotation->phpExtends = $context->getExtends();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'zircote_model';
    }
}
