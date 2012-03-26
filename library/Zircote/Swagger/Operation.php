<?php
/**
 * filecomment
 * package_declaration
 */
require_once 'Zircote/Swagger/Param.php';
/**
 *
 *
 *
 * @category
 * @package
 * @subpackage
 */
class Zircote_Swagger_Operation
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
        $this->_docComment = preg_replace(
            '/\n\s*\* /', null, $this->_operation->getDocComment()
        );
        $this->_docComment = preg_replace('/\s{2}/', null, $this->_docComment);
        $this->_docComment = substr($this->_docComment, 3, -2);
        $this->_getMethod();
        $this->_getPath();
        $this->_getOperation()
            ->_getApiError();
    }
    protected function _getMethod()
    {
        if(preg_match('/(@GET|@PUT|@POST|@DELETE)/', $this->_docComment, $match)){
            $this->results['Method'] = str_replace('@', null, $match[1]);
        }
        return $this;
    }
    protected function _getPath()
    {
        if(preg_match('/@Path ([^@]*)/i', $this->_docComment, $matches)){
            $this->results['Path'] = $matches[1];
        }
        return $this;
    }
    protected function _getOperation()
    {
        if(preg_match_all('/@ApiOperation \(([^)]*)\)/ix',  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                foreach (explode(',', $match) as $value) {
                    $part = explode('=',preg_replace("/(\s{2}|')/",null,$value));
                    $this->results['ApiOperation'][$part[0]] = trim($part[1], ' "');
                }
            }
        }
        return $this;
    }
    protected function _getApiError()
    {
        if(preg_match_all('/@ApiError \(([^)]*)\)/ix',  $this->_docComment, $matches)){
            foreach ($matches[1] as $match) {
                foreach (explode(',', $match) as $value) {
                    $part = explode('=',preg_replace("/(\s{2}|')/",null,$value));
                    $error[$part[0]] = trim($part[1], ' "');
                }
                $this->results['ApiError'][] = $error;
            }
        }
        return $this;
    }
}