<?php
/**
 *
 * @category Swagger
 * @package Swagger
 * @subpackage Model
 */
require_once 'Swagger/AbstractEntity.php';
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 * @subpackage Model
 */
class Swagger_Model extends Swagger_AbstractEntity
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
    public $results = array();
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
        $this->_parseComment()->_getModelId()->_getModelProperties();
    }

    /**
     * @return Swagger_Api
     */
    protected function _parseComment()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_class->getDocComment()
        );
        return $this;
    }
    protected function _getModelId()
    {
        if(preg_match(self::PATTERN_APIMODEL, $this->_docComment, $matches)){
            foreach ($this->_parseParts($matches[1]) as $key => $value) {
                $this->results[$key] = $value;
            }
        }
        return $this;
    }
    protected function _getModelProperties()
    {
        $this->results['properties'] = array();
        if(preg_match_all(self::PATTERN_APIMODELPARAM, $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $prop = array();
                foreach ($this->_parseParts($match) as $key => $value) {
                    $prop[$key] = $value;
                }
                array_push($this->results['properties'], $prop);
            }
        }
        return $this;
    }
    protected function _parseType($value)
    {

    }
    protected function _isRef($value)
    {
        if(preg_match('/$ref:(\w+)$/i', $value, $match)){
            $value = array('$ref' => $match[1]);
        }
        return $value;
    }
}
