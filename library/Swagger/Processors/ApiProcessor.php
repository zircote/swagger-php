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
use Swagger\Logger;
use Swagger\Parser;
use Swagger\Annotations;
use Swagger\Contexts\MethodContext;

/**
 * ApiProcessor
 */
class ApiProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($annotation, $context)
    {
        if (($annotation instanceof \Swagger\Annotations\Api) === false) {
            return;
        }
        if ($annotation->hasPartialId() === false) {
            $resource = $context->resource;
            if ($resource) {
                $resource->apis[] = $annotation;
            } else {
                Logger::notice('Unexpected "' . $annotation->identity() . '", should be inside or after a "Resource" declaration in ' . $context);
            }
        }


        if ($context->is('method')) {
            $resource = $context->resource;

            if ($annotation->path === null && $resource && $resource->resourcePath) { // No path given?
                // Assume method (without Action suffix) on top the resourcePath
                $annotation->path = $resource->resourcePath . '/' . preg_replace('/Action$/i', '', $context->method);
            }
            if ($annotation->description === null) {
                $annotation->description = $context->extractDescription();
            }
            foreach ($annotation->operations as $i => $operation) {
                if ($operation->nickname === null) {
                    $operation->nickname = $context->method;
                    if (count($annotation->operations) > 1) {
                        $operation->nickname .= '_' . $i;
                    }
                }
            }
        }
    }
}
