<?php
/**
 * filecomment
 * package_declaration
 */
require_once 'Zircote/Swagger/AbstractEntity.php';
/**
 *
 *
 *
 * @category
 * @package
 * @subpackage
 */
class Zircote_Swagger_Param extends Zircote_Swagger_AbstractEntity
{
    public $results = array(
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
        $this->_parseParts();
    }
    protected function _parseParts()
    {
        foreach ($this->_getParts($this->_rawComment) as $value) {
            $part = explode('=',preg_replace(self::STRIP_WHITESPACE_APOST,null,$value));
                if(strstr($part[1], ';')){
                    $value = array();
                    foreach (explode(';', $part[1]) as $each) {
                        $value[] = trim($each, ' "');
                    }
                } else {
                    $value = trim($part[1], ' "');
                }
            $this->results[$part[0]] = $value;
        }

    }
    protected function _getParts($string)
    {
        preg_match_all('/="\w+,\w+"/',$string, $match);
        foreach ($match[0] as $parsed) {
            $string = str_replace($parsed, str_replace(',',';', $parsed) , $string);
        }
        return explode(',', $string);
    }
}