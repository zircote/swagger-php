<?php

namespace Swagger;

/**
 * ProcessorManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ProcessorManager
{
    /**
     * @var array<ProcessorInterface>
     */
    private $processors = array();

    /**
     * initialize default processors
     */
    public function initDefaultProcessors()
    {
        // has to be the first one
        $this->add(new Processors\PartialIdProcessor());
        // other processors
        $this->add(new Processors\ApiProcessor());
        $this->add(new Processors\ModelProcessor());
        $this->add(new Processors\PartialProcessor());
        $this->add(new Processors\PropertyProcessor());
        $this->add(new Processors\ResourceProcessor());
    }

    /**
     * Process supported processors.
     *
     * @param Parser      $parser     parser
     * @param object      $annotation annotation
     * @param object|null $context    context
     */
    public function process(Parser $parser, $annotation, $context = null)
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($annotation, $context)) {
                $processor->process($parser, $annotation, $context);
            }
        }
    }

    /**
     * @param Processors\ProcessorInterface $processor processor
     */
    public function add(Processors\ProcessorInterface $processor)
    {
        $this->processors[$processor->getId()] = $processor;
    }
}
