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
    protected $description;
    /**
     * @var bool
     */
    protected $require = false;
    /**
     * @var bool
     */
    protected $allowMultiple = true;
    /**
     * @var string
     */
    protected $dataType;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $paramType;
}
