<?php

namespace Swagger\Processors;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
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
use Swagger\Annotations\Resource;
use Swagger\Processors\ProcessorInterface;

/**
 * ResourceProcessor
 */
class ResourceProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($annotation, $context)
    {
        if (($annotation instanceof Resource) === false) {
            return;
        }
        if ($annotation->hasPartialId() === false) {
            if ($context->is('class')) {
                $context->resource = $annotation; // Expose within the class context
            } else {
                $context->getRootContext()->resource = $annotation; // Expose to the parse/file context
            }
        }
        if ($context->is('class')) {
            if ($annotation->resourcePath === null) { // No resourcePath given ?
                // Assume Classname (without Controller suffix) matches the base route.
                $annotation->resourcePath = '/' . lcfirst(basename(str_replace('\\', '/', $context->class)));
                $annotation->resourcePath = preg_replace('/Controller$/i', '', $annotation->resourcePath);
            }

            if ($annotation->description === null) {
                $annotation->description = $context->extractDescription();
            }
        }
    }
}
