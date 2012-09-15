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
class Produces extends AbstractAnnotation
{

    /**
     * @var array<\Swagger\ContentType>
     */
    protected $contentType = array();

    /**
     *
     */
    public function __construct($value = array())
    {
        foreach (array_shift($value) as $val) {
            array_push($this->contentType, (string)$val);
        }
    }
}

