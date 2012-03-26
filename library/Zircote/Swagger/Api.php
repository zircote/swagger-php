<?php
/**
 * @category   Zircote
 * @package    Swagger
 * @subpackage Api
 */
require_once 'Zircote/Swagger/AbstractEntity.php';
require_once 'Zircote/Swagger/Operation.php';
/**
 *
 *
 *
 * @category   Zircote
 * @package    Swagger
 * @subpackage Api
 */
class Zircote_Swagger_Api extends Zircote_Swagger_AbstractEntity
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
        'path' => null,
        'value' => null,
        'description' => null,
        'produces' => null,
        'operations' => array()
    );
    /**
     *
     * @param Reflector|string $class
     * @throws Exception
     */
    public function __construct($class)
    {
        if(is_object($class)){
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
            ->_getApi()
            ->_getProduces()
            ->_getMethods();
    }
    /**
     * @return Zircote_Swagger_Api
     */
    protected function _parseApi()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_class->getDocComment()
        );
        return $this;
    }
    /**
     * @return Zircote_Swagger_Api
     */
    protected function _getApi()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        preg_match(self::PATTERN_API,  $comment, $matches);
        $this->results = array_merge_recursive($this->results, $this->_parseParts($matches[1]));
        return $this;
    }
    /**
     * @return Zircote_Swagger_Api
     */
    protected function _getProduces()
    {
        $comment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $this->_docComment);
        preg_match(self::PATTERN_PRODUCES,  $comment, $matches);
        foreach (explode(',', $matches[1]) as $value) {
            $result[] = preg_replace(self::STRIP_WHITESPACE_APOST,null,$value);
        }
        $this->results['produces'] = $result;
        return $this;
    }
    /**
     * @return Zircote_Swagger_Api
     */
    protected function _getMethods()
    {
        /* @var $reflectedMethod ReflectionMethod */
        foreach ($this->_class->getMethods(ReflectionMethod::IS_PUBLIC) as $methodName => $reflectedMethod) {
            if(preg_match('/@ApiOperation/i', $reflectedMethod->getDocComment())){
                $operation = new Zircote_Swagger_Operation($reflectedMethod);
                array_push($this->results['operations'],$operation->results);
            }
        }
        return $this;
    }
}