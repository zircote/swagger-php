<?php

namespace Swagger\Processors;

use Swagger\Logger;
use Swagger\Parser;
use Swagger\Contexts\PropertyContext;

/**
 * PropertyProcessor
 *
 * @uses ProcessorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PropertyProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof \Swagger\Annotations\Property;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        if (!$annotation->hasPartialId()) {
            if ($model = $parser->getCurrentModel()) {
                $model->properties[] = $annotation;
            } else {
                if (count($parser->getModels())) {
                    Logger::notice('Unexpected "' . $annotation->identity() . '", make sure the "@SWG\Model()" declaration is directly above the class definition in ' . Annotations\AbstractAnnotation::$context);
                } else {
                    Logger::notice('Unexpected "' . $annotation->identity() . '", should be inside or after a "Model" declaration in ' . Annotations\AbstractAnnotation::$context);
                }
            }
        }

        if ($context instanceof PropertyContext) {
            if ($annotation->name === null) {
                $annotation->name = $context->getProperty();
            }
            if ($annotation->type === null) {
                if (preg_match('/@var\s+(\w+)(\[\])?/i', $context->getDocComment(), $matches)) {
                    $type = $matches[1];
                    $isArray = isset($matches[2]);

                    $map = array(
                        'array' => 'array',
                        'byte' => array('string', 'byte'),
                        'boolean' => 'boolean',
                        'bool' => 'boolean',
                        'int' => 'integer',
                        'integer' => 'integer',
                        'long' => array('integer', 'long'),
                        'float' => array('number', 'float'),
                        'double' => array('number', 'double'),
                        'string' => 'string',
                        'date' => array('string', 'date'),
                        'datetime' => array('string', 'date-time'),
                        '\\datetime' => array('string', 'date-time'),
                        'byte' => array('string', 'byte'),
                        'number' => 'number',
                        'object' => 'object'
                    );
                    if (array_key_exists(strtolower($type), $map)) {
                        $type = $map[strtolower($type)];
                        if (is_array($type)) {
                            if ($annotation->format === null) {
                                $annotation->format = $type[1];
                            }
                            $type = $type[0];
                        }
                    }
                    if ($isArray) {
                        $annotation->type = 'array';
                        if ($annotation->items === null) {
                            $annotation->items = new \Swagger\Annotations\Items(array('value' => $type));
                        }
                    } else {
                        $annotation->type = $type;
                    }
                }
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
        return 'zircote_property';
    }


}
