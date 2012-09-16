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
 *
 */
class ErrorResponses extends AbstractAnnotation
{
    /**
     * @var array|AbstractAnnotation
     */
    public $value = array();

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        if (is_array($this->value) && count($this->value) > 1) {
            /* @var AbstractAnnotation $v */
            foreach ($this->value as $v) {
                $result[] = $v->toArray();
            }
        } elseif(is_array($this->value) && count($this->value) == 1) {
            $this->value = array_pop($this->value);
        }
        if ($this->value instanceof ErrorResponse) {
            $result = $this->value->toArray();
        }
        return $result;
    }
}

