<?php
namespace Swagger\Processors;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @category   Swagger
 * @package    Swagger
 */

use Swagger\Annotations\Items;
use Swagger\Annotations\Property;
use Swagger\Logger;

/**
 * PropertyProcessor
 */
class PropertyProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($annotation, $context)
    {
        if (($annotation instanceof Property) === false) {
            return;
        }
        if (!$annotation->hasPartialId()) {
            if ($context->model) {
                $context->model->properties[] = $annotation;
            } else {
                Logger::notice('Unexpected "' . $annotation->identity() . '", should be inside or after a "Model" declaration in ' . $context);
            }
        }

        if ($context->is('property')) {
            if ($annotation->name === null) {
                $annotation->name = $context->property;
            }
            if ($annotation->type === null) {
                if (preg_match('/@var\s+(\w+)(\[\])?/i', $context->comment, $matches)) {
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
                            $annotation->items = new Items(array('value' => $type));
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
}
