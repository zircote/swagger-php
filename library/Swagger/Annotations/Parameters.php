<?php
namespace Swagger\Annotations;

/**
 * @package
 * @category
 * @subpackage
 */
/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Parameters extends AbstractAnnotation
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
            $result[] = $this->value->toArray();
        } elseif ($this->value) {
            $result[] = array_filter($this->value->toArray(), array($this, 'arrayFilter'));
        }
        return $result;
    }
}

