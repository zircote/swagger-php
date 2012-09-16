<?php
namespace Swagger\Annotations;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Operation;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Operations extends AbstractAnnotation
{
    /**
     * @var array
     */
    public $value = array();

    /**
     * @return array|AbstractAnnotation
     */
    public function toArray()
    {
        $result = array();
        if (is_array($this->value)) {
            /* @var AbstractAnnotation $v */
            foreach ($this->value as $v) {
                $result[] = $v->toArray();
            }
        } elseif ($this->value instanceof Operation) {
            $result = $this->value->toArray();
        }
        return $result;
    }
}

