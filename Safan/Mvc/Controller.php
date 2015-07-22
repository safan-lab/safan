<?php

namespace Safan\Mvc;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class Controller
{
    /**
     * @var string
     */
    private $layout = '';

    /**
     * @var string
     */
    protected $view;

    /**
     * Page Title
     */
    public $pageTitle = 'Safan Framework';

    /**
     * Meta keywords for page
     */
    public $keywords = 'PHP, framework, application for all needs';
    /**
     * Description for page
     */
    public $description = 'Safan - Simple application or all needs';
    /**
     * Meta tags
     */
    public $metaTags = array();

    /**
     * Vars for extract
     */
    public $vars = array();

    /**
     * Assign Vars
     */
    public function assign($key, $value){
        $this->vars[$key] = $value;
    }

    /**
     * @param $layout
     * @param bool $fullPath
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function setLayout($layout, $fullPath = false){
        $layoutPaths = explode(':', $layout);

        if(sizeof($layoutPaths) !== 2)
            throw new FileNotFoundException('Layout is not correct');

        $moduleName     = $layoutPaths[0];
        $layoutFileName = $layoutPaths[1];

        $modules = Safan::handler()->getModules();

        if(!isset($modules[$moduleName]))
            throw new FileNotFoundException('Layout module is not define');

        $layoutFile = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Layouts' . DS . $layoutFileName . '.php';

        if($fullPath === false)
            $layout = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath() . DS . 'Layouts' . $layout;

        if(!file_exists($layoutFile))
            throw new FileNotFoundException('Layout is not exist');

        $this->layout = $layout;
    }

    /**
     * @return string
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function getLayout(){
        if(strlen($this->layout) <= 0)
            $this->layout = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath() . DS . 'Layouts' . DS . 'main.php';

        if(!file_exists($this->layout)){
            // set main layout from SafanResponse Module
            $this->layout = SAFAN_FRAMEWORK_PATH . DS . '..' . DS . 'SafanResponse' . DS . 'Layouts' . DS . 'main.php';

            if(!file_exists($this->layout))
                throw new FileNotFoundException('Safan response path is not defined');
        }
        
        return $this->layout;
    }

    /**
     * @param $view
     * @param bool $fullPath
     */
    protected function render($view, $fullPath = false){
        $layout = $this->getLayout();

        if($fullPath === false){
            $modulePath = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath();
            $contollerName = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentController();
            $view = $modulePath . DS . 'Resources' . DS . 'view' . DS . strtolower($contollerName) . DS . $view . '.php';
        }

        if(!file_exists($view))
            return Safan::handler()->getObjectManager()->get('dispatcher')->dispatchToError(404, 'View file not found');
        $this->view = $view;

        return $this->load($layout, true);
    }

    /**
     * @param $view
     */
    protected function renderPartial($view){
        $modulePath = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath();
        $contollerName = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentController();
        $view = $modulePath . DS . 'Resources' . DS . 'view' . DS . strtolower($contollerName) . DS . $view . '.php';

        if(!file_exists($view))
            return Safan::handler()->getObjectManager()->get('dispatcher')->dispatchToError(404, 'View file not found');

        return $this->load($view, true);
    }

    /**
     * @param $file
     * @param bool $isLayout
     */
    public function load($file, $isLayout = false){
        // set objects
        $this->vars['widgetManager'] = Safan::handler()->getObjectManager()->get('widget');
        $this->vars['logger'] = Safan::handler()->getObjectManager()->get('logger');
        $this->vars['flashMessenger'] = Safan::handler()->getObjectManager()->get('flashMessenger');
        $this->vars['assets'] = Safan::handler()->getObjectManager()->get('assets');
        extract($this->vars, EXTR_REFS);

        if(!$isLayout){
            ob_start();
            include $file;
            $outputBuffer = ob_get_clean();
            echo $outputBuffer;
            ob_end_flush();
        }
        else
            include $file;


        return;
    }

    /**
     *
     */
    public function getContent(){
        Safan::handler()->getObjectManager()->get('eventListener')->runEvent('preLoadView');
        return $this->load($this->view);
    }

    /**
     * Render Json Content
     */
    public function renderJson($params = array()){
        echo json_encode($params);
        exit;
    }

    /**
     * Redirect
     *
     * @param string $url
     * @param bool $globalUrl
     */
    public function redirect($url = '', $globalUrl = false){
        if($globalUrl){
            header('location: ' . $url);
            exit;
        }

        if(!$url)
            header('location: ' . Safan::handler()->baseUrl);
        else
            header('location: ' . Safan::handler()->baseUrl . $url);
        exit;
    }

    /**
     * render meta tags
     *
     */
    public function getMetaTags(){
        $metas = '';

        if(!empty($this->metaTags)){
            foreach($this->metaTags as $meta)
                $metas .= '<meta property="'. $meta["property"] .'" content="'. $meta["content"] .'" />';
        }

        return $metas;
    }
}
