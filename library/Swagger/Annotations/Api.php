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
use Swagger\Annotations\Operations;
use Swagger\Logger;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Api extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string|Operation
     */
    public $operations = array();

    /**
     * @var array
     */
    public $produces;

    /**
     * @var array
     */
    public $consumes;

    /**
     * Undocumented
     * @var bool
     */
    public $deprecated;

    /**
     * @var Undocumented
     */
    public $defaultValue;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Operation' => 'operations[]',
        '\Swagger\Annotations\Produces' => 'produces[]',
        '\Swagger\Annotations\Consumes' => 'consumes[]',
    );

    public function __construct(array $values = array()) {
        parent::__construct($values);
    }

    public function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $index => $annotation) {
            if ($annotation instanceof Operations) {
                foreach ($annotation->operations as $operation) {
                    $this->operations[] = $operation;
                }
                unset($annotations[$index]);
            }
        }
        return parent::setNestedAnnotations($annotations);
    }

    public function validate()
    {
        $operations = array();
        foreach ($this->operations as $operation) {
            if ($operation->validate()) {
                $operations[] = $operation;
            }
        }
        $this->operations = $operations;
        if (count($this->operations) === 0 && count($this->_partials) === 0) {
            Logger::notice('Api "'.$this->path.'" doesn\'t have any valid operations');
            return false;
        }
        Produces::validateContainer($this);
        Consumes::validateContainer($this);
        return true;
    }

    public function identity() {
        return '@SWG\Api(path="'.$this->path.'")';
    }
}
