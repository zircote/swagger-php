<?php

namespace Swagger\Processors;

use Swagger\Logger;
use Swagger\Parser;
use Swagger\Annotations;
use Swagger\Contexts\MethodContext;

/**
 * ApiProcessor
 *
 * @uses ProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ApiProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof \Swagger\Annotations\Api;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        if (!$annotation->hasPartialId()) {
            if ($resource = $parser->getCurrentResource()) {
                $resource->apis[] = $annotation;
            } else {
                Logger::notice('Unexpected "' . $annotation->identity() . '", should be inside or after a "Resource" declaration in ' . Annotations\AbstractAnnotation::$context);
            }
        }

        if ($context instanceof MethodContext) {
            $resource = $context->getResource();

            if ($annotation->path === null && $resource && $resource->resourcePath) { // No path given?
                // Assume method (without Action suffix) on top the resourcePath
                $annotation->path = $resource->resourcePath . '/' . preg_replace('/Action$/i', '', $context->getMethod());
            }
            if ($annotation->description === null) {
                $annotation->description = $context->extractDescription();
            }
            foreach ($annotation->operations as $i => $operation) {
                if ($operation->nickname === null) {
                    $operation->nickname = $context->getMethod();
                    if (count($annotation->operations) > 1) {
                        $operation->nickname .= '_' . $i;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'zircote_api';
    }
}
