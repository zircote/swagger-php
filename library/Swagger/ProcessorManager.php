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
        $this->add(new Processor\PartialIdProcessor());
        // other processors
        $this->add(new Processor\ApiProcessor());
        $this->add(new Processor\ModelProcessor());
        $this->add(new Processor\PartialProcessor());
        $this->add(new Processor\PropertyProcessor());
        $this->add(new Processor\ResourceProcessor());
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
     * @param Processor\ProcessorInterface $processor processor
     */
    public function add(Processor\ProcessorInterface $processor)
    {
        $this->processors[$processor->getId()] = $processor;
    }
}
