<?php
namespace Swagger\Annotations;

/**
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

