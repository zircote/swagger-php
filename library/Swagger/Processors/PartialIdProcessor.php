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
use Swagger\Annotations\AbstractAnnotation;

class PartialIdProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($annotation, $context)
    {
        return $annotation instanceof AbstractAnnotation && $annotation->hasPartialId();
    }

    /**
     * {@inheritdoc}
     */
    public function process(Parser $parser, $annotation, $context)
    {
        $id = $annotation->_partialId;

        if ($parser->hasPartial($id)) {
            Logger::notice('partial="' . $annotation->_partialId . '" is not unique. another was found in ' . Annotations\AbstractAnnotation::$context);
        }

        $parser->setPartial($id, $annotation);
    }
}
