<?php
/**
 * @category   Zircote
 * @package    Swagger
 * @subpackage Api
 */
require_once 'Zircote/Swagger/Operation.php';
/**
 *
 *
 *
 * @category   Zircote
 * @package    Swagger
 * @subpackage Api
 */
class Zircote_Swagger_Api
{
    /**
     *
     * @var ReflectionClass
     */
    protected $_class;
    protected $_docComment;
    public $results = array(
        'Path' => null,
        'Api' => null,
        'Produces' => null,
        'Operations' => array()
    );

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
        $this->_parseApi();
        $this->_getApi();
        $this->_getPath();
        $this->_getProduces();
        $this->_getMethods();
    }
    protected function _parseApi()
    {
        $this->_docComment = $this->_class->getDocComment();
        $this->_getPath();
    }
    protected function _getPath()
    {
        if(preg_match('/@Path (.*)\n/', $this->_docComment, $matches)){
            $this->results['Path'] = $matches[1];
        }
    }
    protected function _getApi()
    {
        preg_match('/@Api (.*)\n/', $this->_docComment, $matches);
        $this->results['Api'] = $matches[1];
    }
    protected function _getProduces()
    {
        $comment = preg_replace('/\n \* /', null, $this->_docComment);
        preg_match('/@Produces \((.*)\)/',  $comment, $matches);
        foreach (explode(',', $matches[1]) as $value) {
            $result[] = preg_replace("/(\s|')/",null,$value);
        }
        $this->results['Produces'] = $result;
    }
    protected function _getMethods()
    {
        /* @var $reflectedMethod ReflectionMethod */
        foreach ($this->_class->getMethods(ReflectionMethod::IS_PUBLIC) as $methodName => $reflectedMethod) {
            if(preg_match('/@ApiOperation/', $reflectedMethod->getDocComment())){
                $operation = new Zircote_Swagger_Operation($reflectedMethod);
                array_push($this->results['Operations'],$operation->results);
            }
        }
    }
}