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
    public function __construct($path)
    {
        $this->_path = $path;
        $this->_getFiles($this->_path);
    }
    public function buildResource()
    {

    }

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
                array_push($this->apis, new Zircote_Swagger_Api($ns. '_' . $class));
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