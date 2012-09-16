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

class Resource extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $apiVersion;

    /**
     * @var string
     */
    public $swaggerVersion;

    /**
     * "http://petstore.swagger.wordnik.com/api"
     *
     * @var string
     */
    public $basePath;

    /**
     * @var "/store"
     */
    public $resourcePath;

    /**
     * @var array
     */
    public $apis = array();

    /**
     * @var array
     */
    public $models = array();
}

