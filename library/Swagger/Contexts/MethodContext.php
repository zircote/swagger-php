<?php

namespace Swagger\Contexts;

use Swagger\Annotations\Resource;

/**
 * MethodContext
 *
 * @uses AbstractContext
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class MethodContext extends AbstractContext
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $docComment;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @param string         $method     method
     * @param string         $docComment docComment
     * @param Resource|false $resource   resource
     */
    public function __construct($method, $docComment, $resource = null)
    {
        $this->method     = $method;
        $this->docComment = $docComment;
        $this->resource   = $resource;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

}
