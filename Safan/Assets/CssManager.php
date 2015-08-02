<?php

namespace Safan\Assets;


use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class CssManager
{

    /**
     * @var string
     */
    private $yuiVersion = '2.4.8';

    /**
     * @var string
     */
    private $cacheDir = '/assets/css';

    /**
     * @var array
     */
    public $css = array();

    /**
     * Check dir for css files
     */
    public function __construct(){
        $cssDir = APP_BASE_PATH . DS . 'resource' . $this->cacheDir;

        if(!is_dir($cssDir)){
            mkdir($cssDir, 0777, true);
            chmod($cssDir, 0777);
        }
    }

    /**
     * @return object
     */
    public function getCss(){
        return $this->css;
    }

    /**
     * @param $files
     * @param $cacheFileName
     * @return bool|void
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function compressFiles($files, $cacheFileName = ''){
        if(empty($files))
            return false;

        $buffer = '';
        foreach ($files as $cssFile) {

            if(!file_exists($cssFile))
                throw new FileNotFoundException('Asset css file not found');

            $buffer .= file_get_contents($cssFile);
        }

        if($cacheFileName == '')
            $cacheFileName = md5(implode('_', $files)) . '.css';

        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);

        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        // Write everything out
        $filePath = APP_BASE_PATH . DS . 'resource' . $this->cacheDir . DS . $cacheFileName;
        $fp = fopen($filePath, 'w');

        if(!$this->fwriteStream($fp, $buffer))
            return false;

        fclose($fp);

        return $this->minify($filePath);
    }

    /**
     * @param $fp
     * @param $string
     * @return int
     */
    private function fwriteStream($fp, $string) {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));
            if ($fwrite === false)
                return $written;
        }

        return $written;
    }

    /**
     * Minify command
     *
     * @param $filePath
     */
    private function minify($filePath){
        shell_exec('java -jar '. dirname(__FILE__) . DS . 'yui' . DS .'yuicompressor-'. $this->yuiVersion .'.jar '. $filePath .' -o ' . $filePath);
    }

    /**
     * @param $files
     * @return bool
     */
    public function checkCustomAssets($files){
        if(empty($files))
            return false;

        $fileName = md5(implode('_', $files)) . '.css';
        $cssFile = APP_BASE_PATH . DS . 'resource' . $this->cacheDir . DS . $fileName;

        if(!file_exists($cssFile))
            $this->compressFiles($files);

        $cacheFileUri = Safan::handler()->baseUrl . $this->cacheDir . DS . $fileName;

        return $this->getCacheFile($cacheFileUri);
    }

    /**
     * @param $fileUrl string
     * @return string
     */
    public function getCacheFile($fileUrl){
        return '<link href="'. $fileUrl .'" type="text/css" rel="stylesheet" />';
    }
}