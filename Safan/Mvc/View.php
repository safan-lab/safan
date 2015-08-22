<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Mvc;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class View
{
    /**
     * Page Title
     */
    public $pageTitle = '';

    /**
     * Meta keywords for page
     */
    public $keywords = '';
    /**
     * Description for page
     */
    public $description = '';

    /**
     * Content cache from buffer
     *
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    private $layoutFile = '';

    /**
     * @var string
     */
    private $viewFile = '';

    /**
     * @var array
     */
    private $styles = [];

    /**
     * @var array
     */
    private $scripts = [];

    /**
     * Meta tags
     *
     * @var array
     */
    private $metaTags = [];

    /**
     * @param $layoutFile
     */
    public function setLayoutFile($layoutFile){
        $this->layoutFile = $layoutFile;
    }

    /**
     * Get layout
     * Requirement: if layout is empty find current module layout. If that empty too, render Safan main module layout.
     *
     * @return string
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    private function getLayoutFile(){
        if(empty($this->layoutFile))
            $this->layoutFile = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath() . DS . 'Layouts' . DS . 'main.php';

        if(!file_exists($this->layoutFile)){
            // set main layout from SafanResponse Module
            $this->layoutFile = SAFAN_FRAMEWORK_PATH . DS . '..' . DS . 'SafanResponse' . DS . 'Layouts' . DS . 'main.php';

            if(!file_exists($this->layoutFile))
                throw new FileNotFoundException('Safan response path is not defined');
        }

        return $this->layoutFile;
    }

    /**
     * @param $viewFile
     */
    public function setViewFile($viewFile){
        $this->viewFile = $viewFile;
    }

    /**
     * @param $vars
     */
    public function loadViewFile($vars){
        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');
        $vars['assets']         = Safan::handler()->getObjectManager()->get('assets');

        extract($vars, EXTR_REFS);

        ob_start();
        include $this->viewFile;
        $outputBuffer  = ob_get_clean();
        $this->content = $outputBuffer;
    }

    /**
     * @return string
     */
    public function getContentFromOutput(){
        return $this->content;
    }

    /**
     * @param $widgetFile
     * @param $vars
     */
    public function loadWidgetFile($widgetFile, $vars){
        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');
        $vars['assets']         = Safan::handler()->getObjectManager()->get('assets');

        extract($vars, EXTR_REFS);

        include $widgetFile;
    }

    /**
     * @param $vars
     */
    public function loadLayoutFile($vars){
        $layoutFile = $this->getLayoutFile();

        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');
        $vars['assets']         = Safan::handler()->getObjectManager()->get('assets');

        extract($vars, EXTR_REFS);

        include $layoutFile;
        ob_end_flush();
    }

    /**
     * Render meta tags
     *
     * @return string
     */
    public function getMetaTags(){
        $metaTags = '';

        if(!empty($this->metaTags)){
            foreach($this->metaTags as $meta)
                $metaTags .= '<meta property="'. $meta["property"] .'" content="'. $meta["content"] .'" />';
        }

        return $metaTags;
    }

    /**
     * Add style
     *
     * @param $style
     */
    public function addStyle($style){
        $this->styles[] = $style;
    }

    /**
     * @return array
     */
    public function getStyles(){
        return $this->styles;
    }

    /**
     * Add script
     *
     * @param $script
     */
    public function addScript($script){
        $this->scripts[] = $script;
    }

    /**
     * @return array
     */
    public function getScripts(){
        return $this->scripts;
    }
}