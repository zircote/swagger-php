<?php
/**
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
require_once 'Zircote/Swagger/Api.php';
/**
 *
 *
 *
 * @category   Zircote
 * @package    Swagger
 * @subpackage Resource
 */
class Zircote_Swagger_Resource
{
    protected $_path;
    public $apis = array();
    public $results = array(
        'apis' => array(),
        'basePath' => 'http://org.local/v1',
        'swagrVersion' => '0.1a',
        'apiVersion' => '1.0.1a'
    );
    /**
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
        $this->_getFiles($this->_path);
        $this->buildResource();
    }
    /**
     * @return array
     */
    public function buildResource()
    {
        /* @var $api Zircote_Swagger_Api */
        foreach ($this->apis as $api) {
            $api = array(
                'path' => $api->results['path'],
                'value' => $api->results['value'],
                'description' => $api->results['description']
            );
            array_push($this->results['apis'],$api);
        }
        return $this->results;
    }
    /**
     *
     * @param string|null $path
     * @return array[Zircote_Swagger_Api]
     */
    protected function _getFiles($path = null)
    {
        if(!$path){
            $path = $this->_path;
        }
        $files = array();
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileInfo) {
            if(!$fileInfo->isDot() && !$fileInfo->isDir()){
                $ns = str_replace($this->_path . '/', null, $path);
                $class = str_replace('.php', null, $fileInfo->getFileName());
                require_once $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName();
                array_push($this->apis, new Zircote_Swagger_Api($ns. '_' . $class, $this->results));
            } elseif(!$fileInfo->isDot() && $fileInfo->isDir()){
                $files = array_merge(
                    $files,
                    $this->_getFiles(
                        $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName()
                    )
                );
            }
        }
        return $files;
    }
}