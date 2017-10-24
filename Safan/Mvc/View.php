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
     *
     * @var string
     */
    public $pageTitle = '';

    /**
     * Meta keywords for page
     *
     * @var string
     */
    public $keywords = '';

    /**
     * Description for page
     *
     * @var string
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
     * @param string $layoutFile
     */
    public function setLayoutFile(string $layoutFile)
    {
        $this->layoutFile = $layoutFile;
    }

    /**
     * Get layout
     * Requirement: if layout is empty find current module layout. If that empty too, render Safan main module layout.
     *
     * @return string
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    private function getLayoutFile()
    {
        if (empty($this->layoutFile)) {
            $this->layoutFile = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath() . DS . 'Layouts' . DS . 'main.php';
        }

        if (!file_exists($this->layoutFile)) {
            // set main layout from SafanResponse Module
            $this->layoutFile = SAFAN_FRAMEWORK_PATH . DS . '..' . DS . 'SafanResponse' . DS . 'Layouts' . DS . 'main.php';

            if (!file_exists($this->layoutFile)) {
                throw new FileNotFoundException('Safan response path is not defined');
            }
        }

        return $this->layoutFile;
    }

    /**
     * @param string $viewFile
     */
    public function setViewFile(string $viewFile)
    {
        $this->viewFile = $viewFile;
    }

    /**
     * @param $vars
     */
    public function loadViewFile(array $vars = [])
    {
        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');

        extract($vars, EXTR_REFS);

        ob_start();
        include $this->viewFile;
        $outputBuffer  = ob_get_clean();
        $this->content = $outputBuffer;
    }

    /**
     * @return string
     */
    public function getContentFromOutput()
    {
        return $this->content;
    }

    /**
     * @param string $widgetFile
     * @param array $vars
     */
    public function loadWidgetFile(string $widgetFile, array $vars = [])
    {
        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');

        extract($vars, EXTR_REFS);

        include $widgetFile;
    }

    /**
     * @param array $vars
     */
    public function loadLayoutFile(array $vars = [])
    {
        $layoutFile = $this->getLayoutFile();

        $vars['widgetManager']  = Safan::handler()->getObjectManager()->get('widget');
        $vars['logger']         = Safan::handler()->getObjectManager()->get('logger');
        $vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');

        extract($vars, EXTR_REFS);

        include $layoutFile;
        ob_end_flush();
    }

    /**
     * Render meta tags
     *
     * @return string
     */
    public function getMetaTags()
    {
        $metaTags = '';

        if (!empty($this->metaTags)) {
            foreach ($this->metaTags as $meta) {
                $metaTags .= '<meta property="'. $meta["property"] .'" content="'. $meta["content"] .'" />';
            }
        }

        return $metaTags;
    }

    /**
     * Add style
     *
     * @param $style
     */
    public function addStyle(string $style)
    {
        $this->styles[] = $style;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Add script
     *
     * @param $script
     */
    public function addScript(string $script)
    {
        $this->scripts[] = $script;
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }
}