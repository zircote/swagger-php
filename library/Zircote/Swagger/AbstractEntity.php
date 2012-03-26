<?php
/**
 * filecomment
 * package_declaration
 */
/**
 *
 *
 *
 * @category
 * @package
 * @subpackage
 */
class Zircote_Swagger_AbstractEntity
{
    const PATTERN_PATH           = '/@Path ([^@]*)/i';
    const STRIP_LINE_PREAMBLE    = '/\n\s*\* /';
    const STRIP_WHITESPACE_APOST = "/(\s{2}|')/";
    const STRIP_WHITESPACE       = '/\s{2}/';
    const PATTERN_API            = '/@Api \(([^@|)]*)\)/i';
    const PATTERN_PRODUCES       = '/@Produces \(([^@|)]*)\)/i';
    const PATTERN_METHOD         = '/(@GET|@PUT|@POST|@DELETE)/';
    const PATTERN_OPERATION      = '/@ApiOperation \(([^)]*)\)/ix';
    const PATTERN_APIERROR       = '/@ApiError \(([^)]*)\)/ix';
    const PATTERN_APIPARAM       = '/@ApiParam \(([^)]*)\)/ix';

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
        return $result;
    }
    /**
     *
     * @param string $string
     * @return array
     */
    protected function _getParts($string)
    {
        preg_match_all('/="\w+,\w+"/',$string, $match);
        foreach ($match[0] as $parsed) {
            $string = str_replace($parsed, str_replace(',',';', $parsed) , $string);
        }
        return explode(',', $string);
    }
}