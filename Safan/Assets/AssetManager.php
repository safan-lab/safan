<?php

namespace Safan\Assets;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class AssetManager
{
    /**
     * @var array
     */
    private $assets = array();

    /**
     * @var string
     */
    private $assetsUri = '';

    /**
     * @var
     */
    private $compressor;

    /**
     * @param $assetsPath
     */
    public function __construct($assetsPath){
        $this->assetsUri = Safan::handler()->baseUrl . '/' . $assetsPath;
        $this->compressor = new Compressor($assetsPath);
    }

    /**
     * Get mapping file data
     */
    public function getCompressor(){
        return $this->compressor;
    }

    /**
     * @return string
     */
    public function getAssets(){
        return implode("\n\r", $this->assets);
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

            $this->assets[] = '<link href="'. $assetLink .'" '. $htmlAttributes .' />';
        }
        else if($extension == 'js'){
            $htmlAttributes = '';
            foreach($attributes as $key => $attr)
                $htmlAttributes .= ' ' . $key . '="' . $attr . '"';

            // check type
            if(!isset($attributes['type']))
                $htmlAttributes .= ' type="text/javascript"';

            $this->assets[] = '<script ' . $htmlAttributes . ' src="'. $assetLink .'"></script>';
        }
        else
            throw new ParamsNotFoundException('Unknown asset type');
    }
}