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
    const PATTERN_PATH = '/@Path ([^@]*)/i';
    const STRIP_LINE_PREAMBLE = '/\n\s*\* /';
    const STRIP_WHITESPACE_APOST = "/(\s{2}|')/";
    const STRIP_WHITESPACE = '/\s{2}/';
    const PATTERN_API = '/@Api ([^@]*)/i';
    const PATTERN_PRODUCES = '/@Produces \(([^@|)]*)\)/';
    const PATTERN_METHOD = '/(@GET|@PUT|@POST|@DELETE)/';
    const PATTERN_OPERATION = '/@ApiOperation \(([^)]*)\)/ix';
    const PATTERN_APIERROR = '/@ApiError \(([^)]*)\)/ix';
    const PATTERN_APIPARAM = '/@ApiParam \(([^)]*)\)/ix';
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
}