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
class Parameter extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $allowMultiple = true;
    /**
     * @var string
     */
    public $dataType;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $paramType;

    /**
     * @var bool
     */
    public $required;

    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var mixed
     */
    public $defaultValue;

    /**
     * @return array
     */
    public function toArray()
    {
        $members =  array_filter((array) $this, array($this, 'arrayFilter'));
        $result = array();
        foreach ($members as $k => $m) {
            if ($m instanceof AllowableValues) {
                $members['allowableValues'] = $m->toArray();
            }
        }
        if (isset($members['value'])) {
            foreach ($members['value'] as $k => $m) {
                if ($m instanceof AbstractAnnotation) {
                    $result[] = $m->toArray();
                }
            }
        }
        if (isset($members['value'])) {
            $members['value'] = $result;
        }
        return $members;
    }
}

