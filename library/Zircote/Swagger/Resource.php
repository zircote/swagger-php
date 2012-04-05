<?php
/**
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Swagger/Api.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
class Swagger_Resource extends Swagger_AbstractEntity
{
    public $results = array();
    protected $_classList;
    /**
     *
     * @param Reflector $path
     */
    public function __construct($classList)
    {
        $this->_classList = $classList;
        $this->_introSpec();
    }
    /**
     *
     * @return Swagger_Resource
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $res = new Swagger_Api($reflectedClass);
            if(isset($res->results['basePath'])){
                $this->results[$res->results['basePath']][$res->results['path']] = $res->results;
            }
        }
        return $this;
    }
    /**
     *
     * @return multitype:
     */
    public function getResources()
    {
        return array_keys($this->results);
    }
    public function getResource($basePath)
    {
        if(!isset($this->results[$basePath])){
            throw new Exception(sprintf('Resource [%s] is not found',$basePath));
        }
        return $this->results[$basePath];
    }
}