<?php
/**
 * @category Swagger
 * @package Swagger
 */
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 */
class Swagger_AbstractEntity
{
    const PATTERN_PATH           = '/@ApiPath\s{0,}([^@]*)/i';
    const STRIP_LINE_PREAMBLE    = '/\n\s*\* /';
    const STRIP_WHITESPACE_APOST = "/(\s{2}|')/";
    const STRIP_WHITESPACE       = '/\s{2}/';
    const PATTERN_METHOD         = '/(@GET|@PUT|@POST|@DELETE)/';
    const PATTERN_OPERATION      = '/@ApiOperation\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIERROR       = '/@ApiError\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIPARAM       = '/@ApiParam\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_API            = '/@Api\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_PRODUCES       = '/@ApiProduces\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_RESOURCE       = '/@ApiResource\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIMODEL       = '/@ApiModel\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIMODELPARAM  = '/@ApiModelProp\s{0,}\(([^@|)]*)\)/i';

    protected $_resource;
    /**
     *
     * @param string $docComment
     * @return string
     */
    protected function _parseDocComment($docComment)
    {
        $docComment = substr($docComment, 3, -2);
        $docComment = preg_replace(self::STRIP_LINE_PREAMBLE, null, $docComment);
        $docComment = preg_replace(self::STRIP_WHITESPACE, null, $docComment);
        return $docComment;
    }
    /**
     *
     * @param string $parameter
     * @return array
     */
    protected function _parseParts($parameter)
    {
        $results = array();

        foreach ($this->_getParts($parameter) as $value) {
            $part = explode('=',preg_replace(self::STRIP_WHITESPACE_APOST,null,$value));
            if(isset($part[1])){
            if(strstr($part[1], ';')){
                $value = array();
                foreach (explode(';', $part[1]) as $each) {
                    $value[] = trim($each, ' "');
                }
            } else {
                $value = trim($part[1], ' "');
            }
            $result[$part[0]] = $value;
            }
        }
        return $this->_parseItems($result);
    }
    /**
     *
     * @param string $string
     * @return array
     */
    protected function _getParts($string)
    {
        if(preg_match_all('/="\w+,\w+"/i',$string, $match)){
            foreach ($match[0] as $parsed) {
                $string = str_replace($parsed, str_replace(',',';', $parsed) , $string);
            }
        }
        if(preg_match_all('/enum="(.*)"/ixu',$string, $match)){
            foreach ($match[0] as $parsed) {
                $string = str_replace($parsed, str_replace(',',';', $parsed) , $string);
            }
        }
        return explode(',', $string);
    }
    protected function _parseItems($items)
    {
        if(key_exists('items', $items)){
            if(preg_match('/(\$ref:|type:)/', $items['items'])){
                $parts = explode(':', $items['items']);
                $items['items'] = array($parts[0] => $parts[1]);
            }
        }
        return $items;
    }
}