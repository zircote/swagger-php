<?php
namespace Swagger;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2012] [Robert Allen]
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
 * @category   Swagger
 * @package    Swagger
 */
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;

/**
 * @category   Swagger
 * @package    Swagger
 */
class Swagger
{

    /**
     *
     * @var Array
     */
    protected $fileList;
    /**
     * @var null
     */
    protected $excludePath;
    protected $classlist = array();
    public $result = array();
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param null $path
     * @param null $excludePath
     */
    public function __construct($path = null, $excludePath = null)
    {
        if ($path) {
            $this->path = $path;
            $this->excludePath = $excludePath;
        }
        $this->discoverServices();
    }

    /**
     * @return Swagger
     */
    protected function initializeAnnotations()
    {
        if (!$this->reader) {
            AnnotationRegistry::registerAutoloadNamespace(
                'Swagger\\',
                dirname(__DIR__)
            );
            $this->reader = new IndexedReader(new AnnotationReader());
        }
        return $this;
    }

    protected function discoverClassAnnotations()
    {
        $this->initializeAnnotations();
        $reader = new AnnotationReader();
        /* @var \ReflectionClass $class */
        foreach ($this->classlist as $class) {
            $result = array();
            $methods = array();
            $properties = null;
            $result = array_merge($result, $reader->getClassAnnotations($class));
            /* @var \ReflectionMethod $method */
            foreach ($class->getMethods() as $method) {
                $methods[$method->getName()] = $reader->getMethodAnnotations($method);
            }
            $result['methods'] = $methods;
            /* @var \ReflectionProperty $property */
            foreach ($class->getProperties() as $property) {
                $properties[$property->getName()] = $reader->getPropertyAnnotations($property);
            }
            $result['properties'] = $properties;
            $this->result[$class->getName()] = $result;
        }

    }

    protected function discoverMethodAnnotations()
    {
        
    }
    protected function discoverPropertyAnnotations()
    {
        
    }

    /**
     * @static
     *
     * @param      $path
     * @param null $excludePath
     *
     * @return Swagger
     */
    public static function discover($path, $excludePath = null)
    {
        $swagger = new self($path, $excludePath);
        return $swagger;
    }

    /**
     *
     * @return array
     */
    public function getFileList()
    {
        if (!$this->fileList) {
            $this->setFileList($this->getFiles());
        }
        return $this->fileList;
    }

    /**
     *
     * @param  array $fileList
     *
     * @return Swagger
     */
    public function setFileList($fileList)
    {
        $this->fileList = $fileList;
        return $this;
    }

    /**
     * @param null $path
     *
     * @return array
     */
    protected function getFiles($path = null)
    {
        if (!$path) {
            $path = $this->path;
        }
        $excludePaths = isset($this->excludePath) ? explode(':', $this->excludePath) : array();
        $files = array();
        $dir = new \DirectoryIterator($path);
        /* @var $fileInfo \DirectoryIterator */
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot()) {
                $skip = false;
                foreach ($excludePaths as $excludePath) {
                    if (strpos(realpath($fileInfo->getPathname()), $excludePath) === 0) {
                        $skip = true;
                        break;
                    }
                }
                if (true === $skip) {
                    continue;
                }
            }
            if (!$fileInfo->isDot() && !$fileInfo->isDir()) {
                if (in_array($fileInfo->getExtension(), array('php','phtml'))) {
                    array_push($files, $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName());
                }
            } elseif (!$fileInfo->isDot() && $fileInfo->isDir()) {
                $files = array_merge($files, $this->getFiles($path . DIRECTORY_SEPARATOR . $fileInfo->getFileName()));
            }
        }
        return $files;
    }

    /**
     * @param $filename
     *
     * @return array
     */
    protected function getClasses($filename)
    {
        $classes = array();
        if (file_exists($filename)) {
            $tokens = token_get_all(file_get_contents($filename));
            $count = count($tokens);
            $namespace = $this->getNamespace($filename);
            for ($i = 2; $i < $count; $i++) {
                if ($tokens[$i - 2][0] == T_CLASS &&
                    $tokens[$i - 1][0] == T_WHITESPACE &&
                    $tokens[$i][0] == T_STRING
                ) {
                    $classes[] = $namespace . $tokens[$i][1];
                }
            }
        }
        return $classes;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    protected function getNamespace($filename)
    {
        $namespace = '\\';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            if (strpos($content, 'namespace') !== false) {
                $tokens = token_get_all($content);
                $startIndex = null;
                $lineNumber = null;

                foreach ($tokens as $index => $token) {
                    if (isset($token[0]) && T_NAMESPACE == $token[0]) {
                        $startIndex = $index + 1;
                        $lineNumber = $token[2];
                        continue;
                    }

                    if (null !== $startIndex && $index > $startIndex) {
                        if (T_STRING === $token[0] || T_NS_SEPARATOR === $token[0]) {
                            if (T_NS_SEPARATOR !== $token[0]) {
                                $namespace .= $token[1] . '\\';
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }

        return $namespace;
    }

    /**
     *
     * @return Swagger
     */
    protected function discoverServices()
    {
        foreach ($this->getFileList() as $filename) {
            if ($filename) {
                include_once $filename;
            }
            foreach ($this->getClasses($filename) as $class) {
                array_push($this->classlist, new \ReflectionClass($class));
            }
        }
        $this->discoverClassAnnotations();
        return $this;
    }

    /**
     * @param      $data
     * @param bool $prettyPrint
     *
     * @return mixed|null|string
     */
    public function jsonEncode($data, $prettyPrint = false)
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            $json = str_replace('\/', '/', json_encode($data));
        }
        if (!$prettyPrint) {
            return $json;
        }
        /* @see Zend_Json::prettyPrint */
        $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = null;
        $indentTotal = 0;
        $lineBreak = "\n";
        $indent = '    ';
        $indentLine = false;
        foreach ($tokens as $token) {
            if ($token == '') {
                continue;
            }
            $preText = str_repeat($indent, $indentTotal);
            if (!$indentLine && ($token == '{' || $token == '[')) {
                $indentTotal++;
                if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
                    $result .= $preText;
                }
                $result .= $token . $lineBreak;
            } elseif (!$indentLine && ($token == '}' || $token == ']')) {
                $indentTotal--;
                $preText = str_repeat($indent, $indentTotal);
                $result .= $lineBreak . $preText . $token;
            } elseif (!$indentLine && $token == ',') {
                $result .= $token . $lineBreak;
            } else {
                $result .= ($indentLine ? '' : $preText) . $token;
                if ((substr_count($token, '"') - substr_count($token, '\"')) % 2 != 0) {
                    $indentLine = !$indentLine;
                }
            }
        }
        return $result;
    }

    /**
     *
     * @param \Doctrine\Common\Annotations\Reader $reader
     *
     * @return Swagger
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getReader()
    {
        return $this->reader;
    }
}

