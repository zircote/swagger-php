<?php

namespace Swagger\Context;

use Swagger\Annotations\Resource;

/**
 * PropertyContext
 *
 * @uses AbstractContext
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PropertyContext extends AbstractContext
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $docComment;

    /**
     * @param string $property     property
     * @param string $docComment docComment
     */
    public function __construct($property, $docComment)
    {
        $this->property     = $property;
        $this->docComment = $docComment;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }
}
