<?php

namespace Swagger\Processors;

use Swagger\Parser;
use Swagger\Contexts\ClassContext;

/**
 * ResourceProcessor
 *
 * @uses ProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ResourceProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof \Swagger\Annotations\Resource;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        if (!$annotation->hasPartialId()) {
            $parser->appendResource($annotation);
        }

        if ($context instanceof ClassContext) {
            if ($annotation->resourcePath === null) { // No resourcePath given ?
                // Assume Classname (without Controller suffix) matches the base route.
                $annotation->resourcePath = '/' . lcfirst(basename(str_replace('\\', '/', $context->getClass())));
                $annotation->resourcePath = preg_replace('/Controller$/i', '', $annotation->resourcePath);
            }

            if ($annotation->description === null) {
                $annotation->description = $context->extractDescription();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'zircote_resource';
    }
}
