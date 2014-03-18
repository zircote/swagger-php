<?php
namespace Swagger\Annotations;

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
 *
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Produces;
use Swagger\Annotations\Resource;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Consumes extends AbstractAnnotation
{
    /**
     * @var array
     */
    public $mimetype = 'string';

    public function jsonSerialize()
    {
        return $this->mimetype;
    }

    protected function setNestedValue($value)
    {
        $this->mimetype = $value;
    }

    /**
     *
     * @param Resource|Api|Operation $annotation
     * @return bool
     */
    public static function validateContainer($annotation)
    {
        if (is_string($annotation->consumes)) {
            $mimetypes = self::decode($annotation->consumes);
            $annotation->consumes = array();
            foreach ($mimetypes as $mimetype) {
                $annotation->consumes[] = new Produces(array('mimetype' => $mimetype));
            }
        }
        return true;
    }
}
