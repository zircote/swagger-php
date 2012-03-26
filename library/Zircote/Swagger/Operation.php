<?php
/**
 * filecomment
 * package_declaration
 */
require_once 'Zircote/Swagger/AbstractEntity.php';
require_once 'Zircote/Swagger/Param.php';
/**
 *
 *
 *
 * @category
 * @package
 * @subpackage
 */
class Zircote_Swagger_Operation extends Zircote_Swagger_AbstractEntity
{
    /**
     *
     * @var ReflectionMethod
     */
    protected $_operation;
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
        'httpMethod' => null,
        'open' => false,
        'deprecated' => false,
        'tags' => array(),
        'path' => null,
        'summary' => null,
        'errorResponses' => array(),
        'parameters' => array()
    );
    /**
     *
     * @param Reflector|string $operation
     * @throws Exception
     */
    public function __construct($operation)
    {
        if($operation instanceof Reflector){
            if(!method_exists($operation, 'getDocComment')){
                throw new Exception('Reflector does not possess a getDocComment method');
            }
            $this->_operation = $operation;
        } elseif(is_string($operation)){
            $this->_operation = new ReflectionClass($operation);
        } else {
            throw new Exception('Incompatable Type attempted to reflect');
        }
        $this->_parse();
    }
    /**
     *
     */
    protected function _parse()
    {
        $this->_docComment = $this->_parseDocComment(
            $this->_operation->getDocComment()
        );
        $this->_getMethod()
            ->_getPath()
            ->_getOperation()
            ->_getApiError()
            ->_getParam();
    }
    /**
     * @return Zircote_Swagger_Operation
     */
    protected function _getMethod()
    {
        if(preg_match(self::PATTERN_METHOD, $this->_docComment, $match)){
            $this->results['httpMethod'] = str_replace('@', null, $match[1]);
        }
        return $this;
    }
    /**
     * @return Zircote_Swagger_Operation
     */
    protected function _getPath()
    {
        if(preg_match(self::PATTERN_PATH, $this->_docComment, $matches)){
            $this->results['path'] = $matches[1];
        }
        return $this;
    }
    /**
     * @return Zircote_Swagger_Operation
     */
    protected function _getOperation()
    {
        if(preg_match_all(self::PATTERN_OPERATION,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $this->results = array_merge_recursive($this->results, $this->_parseParts($match));
            }
        }
        return $this;
    }
    /**
     * @return Zircote_Swagger_Operation
     */
    protected function _getApiError()
    {
        if(preg_match_all(self::PATTERN_APIERROR,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                array_push($this->results['errorResponses'],$this->_parseParts($match));
            }
        }
        return $this;
    }
    /**
     * @return Zircote_Swagger_Operation
     */
    protected function _getParam()
    {
        if(preg_match_all(self::PATTERN_APIPARAM, $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $apiOperation = new Zircote_Swagger_Param($match);
                array_push($this->results['parameters'],$apiOperation->results);
            }
        }
        return $this;
    }
}