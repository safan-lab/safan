<?php

namespace Safan\Assets;

use Safan\GlobalData\Get;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class AssetManager
{
    /**
     * @var string
     */
    private $assetsUri = '';

    /**
     * @var
     */
    private $compressor;

    /**
     * @var object
     */
    private $cssManager;

    /**
     * @var object
     */
    private $jsManager;

    /**
     * @param $assetsPath
     */
    public function __construct($assetsPath){
        $this->assetsUri  = Safan::handler()->baseUrl . '/' . $assetsPath;
        $this->compressor = new Compressor($assetsPath);
        $this->cssManager = new CssManager();
        $this->jsManager  = new JsManager();
    }

    /**
     * Get mapping file data
     */
    public function getCompressor(){
        return $this->compressor;
    }

    /**
     * @param $filePath
     * @param array $attributes
     * @return string
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function __invoke($filePath, $attributes = array()){
        $asset = explode(':', $filePath);
        if(sizeof($asset) !== 2)
            throw new FileNotFoundException('Css asset name is not correct');

        $moduleName = $asset[0];
        $filePath = $asset[1];
        $modules = Safan::handler()->getModules();

        if(!isset($modules[$moduleName]))
            throw new FileNotFoundException('Asset module is not define');

        $fullPath = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Resources' . DS . 'public' . DS . $filePath;

        if(!file_exists($fullPath))
            throw new FileNotFoundException('Asset file is not define');

        // get file extension
        $extension = end(explode('.', $fullPath));

        $assetLink = $this->assetsUri . '/' . strtolower($moduleName) . '/' . $filePath;

        if($extension == 'css'){
            $htmlAttributes = '';
            foreach($attributes as $key => $attr)
                $htmlAttributes .= ' ' . $key . '="' . $attr . '"';

            // check rel and type
            if(!isset($attributes['rel']))
                $htmlAttributes .= ' rel="stylesheet"';

            if(!isset($attributes['type']))
                $htmlAttributes .= ' type="text/css"';

            return '<link href="'. $assetLink .'" '. $htmlAttributes .' />';
        }
        else if($extension == 'js'){
            $htmlAttributes = '';
            foreach($attributes as $key => $attr)
                $htmlAttributes .= ' ' . $key . '="' . $attr . '"';

            // check type
            if(!isset($attributes['type']))
                $htmlAttributes .= ' type="text/javascript"';

            return '<script ' . $htmlAttributes . ' src="'. $assetLink .'"></script>';
        }
        else
            throw new ParamsNotFoundException('Unknown asset type');
    }

    /**
     * Minify files
     *
     * @param $assetFiles
     * @param $assetType
     * @return bool
     */
    public function minify($assetFiles, $assetType){
        // convert and return file paths
        $assetFiles = $this->translator($assetFiles);

        if($assetType == 'css')
            return $this->cssManager->checkCustomAssets($assetFiles);
        elseif($assetType == 'js')
            return $this->jsManager->checkCustomAssets($assetFiles);
    }

    /**
     * Translate path
     *
     * @param $files
     * @return array
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    private function translator($files){
        // empty array for return
        $fileArray = array();
        // get modules
        $modules = Safan::handler()->getModules();

        foreach($files as $filePath){
            $asset = explode(':', $filePath);
            if(sizeof($asset) !== 2)
                throw new FileNotFoundException('Css asset name is not correct');

            $moduleName = $asset[0];
            $filePath = $asset[1];

            if(!isset($modules[$moduleName]))
                throw new FileNotFoundException('Asset module is not define');

            $fullPath = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Resources' . DS . 'public' . DS . $filePath;

            if(!file_exists($fullPath))
                throw new FileNotFoundException('Asset file is not define');

            $fileArray[] = $fullPath;
        }

        return $fileArray;
    }
}
