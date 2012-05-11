<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category Swagger
 * @package Swagger
 */
namespace Swagger;
/**
 *
 *
 *
 * @category Swagger
 * @package Swagger
 */
class AbstractEntity
{
    const PATTERN_PATH           = '/@SwaggerPath\s{0,}([^@]*)/i';
    const STRIP_LINE_PREAMBLE    = '/\n\s*\t*\* ?/';
	const STRIP_WHITESPACE_APOST = "/(\s{2}|')/";
    const STRIP_WHITESPACE       = '/\s{2}/';
    const PATTERN_METHOD         = '/(@GET|@PUT|@POST|@DELETE)/';
    const PATTERN_OPERATION      = '/@SwaggerOperation\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIERROR       = '/@SwaggerError\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIPARAM       = '/@SwaggerParam\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_API            = '/@Swagger\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_PRODUCES       = '/@SwaggerProduces\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_RESOURCE       = '/@SwaggerResource\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIMODEL       = '/@SwaggerModel\s{0,}\(([^@|)]*)\)/i';
    const PATTERN_APIMODELPARAM  = '/@property\s{0,}([^@|)]*)/i';

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