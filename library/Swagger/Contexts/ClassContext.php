<?php

namespace Swagger\Contexts;

/**
 * Context
 *
 * @uses AbstractContext
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ClassContext extends AbstractContext
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $extends;

    /**
     * @var string
     */
    private $docComment;

    /**
     * @param string $class      class
     * @param string $extends    extends
     * @param string $docComment docComment
     */
    public function __construct($class, $extends, $docComment)
    {
        $this->class = $class;
        $this->extends = $extends;
        $this->docComment = $docComment;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }
}
