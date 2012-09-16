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
class Model extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $properties = array();
}

