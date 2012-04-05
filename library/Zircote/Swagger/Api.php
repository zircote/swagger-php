<?php
/**
 * @category   Swagger
 * @package    Swagger
 * @subpackage Api
 */
require_once 'Swagger/AbstractEntity.php';
require_once 'Swagger/Operation.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Api
 */
class Swagger_Api extends Swagger_AbstractEntity
{
    /**
     *
     * @var ReflectionClass
     */
    protected $_class;
    /**
     *
     * @var string
     */
    protected $_docComment;
    /**
     *
     * @var array
     */
    public $results = array(
        'operations' => array()
    );
    /**
     *
     * @param Reflector|string $class
     * @throws Exception
     */
    public function __construct($class)
    {
        if(is_object($class) && !$class instanceof Reflector){
            $this->_class = new ReflectionClass($class);
        } elseif($class instanceof Reflector){
            if(!method_exists($class, 'getDocComment')){
                throw new Exception('Reflector does not possess a getDocComment method');
            }
            $this->_class = $class;
        } elseif(is_string($class)){
            $this->_class = new ReflectionClass($class);
        } else {
            throw new Exception('Incompatable Type attempted to reflect');
        }
        $this->_parseApi()
            ->_getResource()
            ->_getApi()
            ->_getProduces()
            ->_getMethods();
    }
    /**
     * @return Swagger_Api
     */
    protected function _parseApi()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_class->getDocComment()
        );
        return $this;
    }
    /**
     * @return Swagger_Api
     */
    protected function _getResource()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_RESOURCE,  $comment, $matches)){
            foreach ($this->_parseParts($matches[1]) as $key => $value) {
                $this->results[$key] = $value;
            }

        }
        return $this;
    }
    /**
     * @return Swagger_Api
     */
    protected function _getApi()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_API,  $comment, $matches)){
            $this->results = array_merge_recursive($this->results, $this->_parseParts($matches[1]));
            $this->results['path'] = $this->results['path'];
        }
        return $this;
    }
    /**
     * @return Swagger_Api
     */
    protected function _getProduces()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        if(preg_match(self::PATTERN_PRODUCES,  $comment, $matches)){
            foreach (explode(',', $matches[1]) as $value) {
                $result[] = preg_replace(self::STRIP_WHITESPACE_APOST,null,$value);
            }
            $this->results['produces'] = $result;
        }
        return $this;
    }
    /**
     * @return Swagger_Api
     */
    protected function _getMethods()
    {
        /* @var $reflectedMethod ReflectionMethod */
        foreach ($this->_class->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectedMethod) {
            if(preg_match('/@ApiOperation/i', $reflectedMethod->getDocComment())){
                $operation = new Swagger_Operation($reflectedMethod, $this->results);
                array_push($this->results['operations'],$operation->results);
            }
        }
        return $this;
    }
}