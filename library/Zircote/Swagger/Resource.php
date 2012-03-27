<?php
/**
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Zircote/Swagger/Api.php';
/**
 *
 *
 *
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
class Zircote_Swagger_Resource extends Zircote_Swagger_AbstractEntity
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
     * @return Zircote_Swagger_Resource
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $res = new Zircote_Swagger_Api($reflectedClass);
            if(isset($res->results['basePath'])){
                $this->results[$res->results['basePath']][$res->results['path']] = $res->results;
            }
        }
        return $this;
    }
}