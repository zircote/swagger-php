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
        if (is_array($this->value)) {
            /* @var AbstractAnnotation $v */
            foreach ($this->value as $k => $v) {
                $result[] = $v->toArray();
            }
        }
        if ($this->value instanceof ErrorResponse) {
            $result = $this->value->toArray();
        }
        if(isset($result['code'])){
            $result = array($result);
        }
        return $result;
    }
}

