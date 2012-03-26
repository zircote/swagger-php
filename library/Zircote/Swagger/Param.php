<?php
/**
 * filecomment
 * package_declaration
 */
class Zircote_Swagger_Param
{
    protected $_params = array(
        'description' => '',
        'required' => false,
        'allowMultiple' => false,
        'allowedValues' => array(),
        'dataType' => null,
        'name' => null,
        'paramType' => null
    );
    protected $_rawComment;
    public function __construct($apiParam)
    {
        $this->_rawComment = $apiParam;
    }
}