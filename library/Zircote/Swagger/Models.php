<?php
/**
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Zircote/Swagger/Model.php';
/**
 *
 *
 *
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
class Zircote_Swagger_Models extends Zircote_Swagger_AbstractEntity
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
     * @return Zircote_Swagger_Models
     */
    protected function _introSpec()
    {
        foreach ($this->_classList as $reflectedClass) {
            $ref = new Zircote_Swagger_Model($reflectedClass);
            if(isset($ref->results['id'])){
                $this->results[$ref->results['id']] = $ref->results;
            }
        }
        return $this;
    }
}