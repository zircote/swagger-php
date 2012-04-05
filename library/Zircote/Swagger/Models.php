<?php
/**
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Swagger/Model.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 * @subpackage Resource
 */
class Swagger_Models extends Swagger_AbstractEntity
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
     * @return Swagger_Models
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $ref = new Swagger_Model($reflectedClass);
            if(isset($ref->results['id'])){
                $this->results[$ref->results['id']] = $ref->results;
            }
        }
        return $this;
    }
}