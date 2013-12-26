<?php

namespace Swagger\Processors;

use Swagger\Parser;

/**
 * ProcessorInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ProcessorInterface
{
    /**
     * @param object      $annotation annotation
     * @param object|null $context    context
     *
     * @return boolean
     */
    public function supports($annotation, $context);

    /**
     * @param Parser      $parser     parser
     * @param object      $annotation annotation
     * @param object|null $context    context
     */
    public function process(Parser $parser, $annotation, $context);

    /**
     * Unique identifier.
     *
     * @return scalar
     */
    public function getId();
}
