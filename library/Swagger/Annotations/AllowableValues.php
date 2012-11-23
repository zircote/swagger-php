<?php
namespace Swagger\Annotations;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2012] [Robert Allen]
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
use \Doctrine\Common\Annotations\AnnotationException;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class AllowableValues extends AbstractAnnotation
{
    /**
     *
     */
    const TYPE_LIST = 'LIST';

    /**
     *
     */
    const TYPE_RANGE = 'RANGE';

    /**
     * @var string
     */
    public $valueType;

    /**
     * @var array
     */
    public $values = array();

    /**
     * @var int|null
     */
    public $min;

    /**
     * @var int|null
     */
    public $max;

    /**
     * @param array $values
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct($values = array())
    {
        switch (strtoupper($values['valueType'])) {
            case self::TYPE_LIST:
                $this->isList($values);
                break;
            case self::TYPE_RANGE:
                $this->isRange($values);
                break;
            default:
                throw new AnnotationException(
                    'Acceptable Values are [LIST|RANGE] for [@AllowableValues]'
                );
        }
    }

    /**
     * @param $values
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function isList($values)
    {
        $this->valueType = self::TYPE_LIST;
        $this->values = $this->decode($values['values']);
        if( $this->values instanceof \stdClass){
            $newValues = array();
            foreach ((array) $this->values as $key => $value) {
                $value = "{$key}-{$value}";
                array_push($newValues, $value);
            }
            $this->values = $newValues;
        }
    }

    /**
     * @param $values
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function isRange($values)
    {
        $this->valueType = self::TYPE_RANGE;
        if (isset($values['min'])) {
            $this->min = $values['min'];
        } else {
            throw new AnnotationException('RANGE types must have min|max declared');
        }
        if (isset($values['max'])) {
            $this->max = $values['max'];
        } else {
            throw new AnnotationException('RANGE types must have min|max declared');
        }
    }
}

