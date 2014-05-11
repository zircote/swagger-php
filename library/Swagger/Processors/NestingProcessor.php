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

use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Partial;
use Swagger\Logger;
use Swagger\Processors\ProcessorInterface;

/**
 * Check for Annotations that should be nested inside other annotations.
 */
class NestingProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($annotation, $context)
    {
        if ($annotation instanceof AbstractAnnotation && $annotation->hasPartialId() === false) {
            $whitelist = array(
                'Swagger\Annotations\Resource',
                'Swagger\Annotations\Api',
                'Swagger\Annotations\Model',
                'Swagger\Annotations\Property'
            );
            if (in_array(get_class($annotation), $whitelist)) {
                return;
            }
            $nesting = array(
                'Swagger\Annotations\Operations' => '@SWG\Api()',
                'Swagger\Annotations\Operation' => '@SWG\Api() or @SWG\Operations()',
                'Swagger\Annotations\Parameters' => '@SWG\Operation()',
                'Swagger\Annotations\Parameter' => '@SWG\Operation()',
            );
            $parent = @$nesting[get_class($annotation)];
            if (!$parent) {
                $parent = 'annotation';
            }
            Logger::notice($annotation->identity() . ' is not placed inside an '.$parent.' in ' . $context);
        }
    }
}
