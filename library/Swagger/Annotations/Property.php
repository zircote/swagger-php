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
class Property extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var array
     */
    public $items;
}

