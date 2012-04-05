<?php
/**
 * filecomment
 * package_declaration
 */
require_once 'Swagger/AbstractEntity.php';
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 * @subpackage Param
 */
class Swagger_Param extends Swagger_AbstractEntity
{
    /**
     *
     * @var array
     */
    public $results = array();
    /**
     *
     * @var string
     */
    protected $_rawComment;
    /**
     *
     * @param string $apiParam
     */
    public function __construct($apiParam)
    {
        $this->_rawComment = $apiParam;
        $this->results = $this->_parseParts($this->_rawComment);
    }
}