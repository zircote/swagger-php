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
    protected $apiVersion;

    /**
     * @var string
     */
    protected $swaggerVersion;

    /**
     * "http://petstore.swagger.wordnik.com/api"
     *
     * @var string
     */
    protected $basePath;

    /**
     * @var "/store"
     */
    protected $resourcePath;
}

