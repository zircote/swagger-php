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
    protected $_docComment;
    public $results = array(
        'Method' => null,
        'Path' => null,
        'ApiOperation' => array(
            'value' => null,
            'responseClass' => null,
            'multiValueResponse' => false,
            'tags' => array()
        ),
        'ApiError' => array(),
        'ApiParam' => array()
    );
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
    protected function _getMethod()
    {
        if(preg_match(self::PATTERN_METHOD, $this->_docComment, $match)){
            $this->results['Method'] = str_replace('@', null, $match[1]);
        }
        return $this;
    }
    protected function _getPath()
    {
        if(preg_match(self::PATTERN_PATH, $this->_docComment, $matches)){
            $this->results['Path'] = $matches[1];
        }
        return $this;
    }
    protected function _getOperation()
    {
        if(preg_match_all(self::PATTERN_OPERATION,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                foreach (explode(',', $match) as $value) {
                    $part = explode('=',preg_replace(self::STRIP_WHITESPACE_APOST,null,$value));
                    $this->results['ApiOperation'][$part[0]] = trim($part[1], ' "');
                }
            }
        }
        return $this;
    }
    protected function _getApiError()
    {
        if(preg_match_all(self::PATTERN_APIERROR,  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                foreach (explode(',', $match) as $value) {
                    $part = explode('=',preg_replace(self::STRIP_WHITESPACE_APOST,null,$value));
                    $error[$part[0]] = trim($part[1], ' "');
                }
                $this->results['ApiError'][] = $error;
            }
        }
        return $this;
    }
    protected function _getParam()
    {
        if(preg_match_all(self::PATTERN_APIPARAM, $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                $apiOperation = new Zircote_Swagger_Param($match);
                array_push($this->results['ApiParam'],$apiOperation->results);
            }
        }
        return $this;
    }
}