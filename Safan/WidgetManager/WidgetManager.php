<?php

namespace Safan\WidgetManager;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class WidgetManager
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @var array
     */
    public $params = array();

    /**
     * @var array
     */
    private $currentWidgetRouting = [];

    /**
     * @var array
     */
    private $loadedWidgetsData = [];

    /**
     * Get config widgets list
     */
    public function __construct(){
        // config file path
        $configFile = APP_BASE_PATH . DS . 'application' . DS . 'Settings' . DS . 'widgets.config.php';
        if(!file_exists($configFile))
            throw new FileNotFoundException('Widgets config file is not exist');

        $this->config = include($configFile);
    }

    /**
     * @param $widgetName
     * @param array $params
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function begin($widgetName, $params = array()){
		if(!isset($this->config[$widgetName]))
            throw new FileNotFoundException($widgetName . ' Widget is not exist');

        $widget = $this->config[$widgetName];

        if(!isset($widget['module']) || !isset($widget['controller']) || !isset($widget['action']))
            throw new ParamsNotFoundException($widgetName . ' Params is not exist');

        // load widget
        $this->loadWidget($widgetName, $widget['module'], $widget['controller'], $widget['action'], $params);
	}

    /**
     * @param $widgetName
     * @param $module
     * @param $controller
     * @param $action
     * @param array $params
     * @return mixed
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     */
    public function loadWidget($widgetName, $module, $controller, $action, $params = array()){
        // get all modules
        $modules = Safan::handler()->getModules();

        if(isset($modules[$module]) && is_dir(APP_BASE_PATH . DS . $modules[$module])){
            $nameSpace  = '\\' . $module;
            $modulePath = APP_BASE_PATH . DS . $modules[$module];
        }
        elseif(isset($modules[ucfirst(strtolower($module))]) && is_dir(APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))])){ // check case sensitivity
            $nameSpace  = '\\' . ucfirst(strtolower($module));
            $modulePath = APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))];
        }
        else
            throw new ParamsNotFoundException('Widget ' . $module . ' module or path are not exist');

        // Controller Class Name
        $moduleController = ucfirst(strtolower($controller)) . 'Controller';
        $controllerFile   = $modulePath . DS . 'Controllers' . DS . $moduleController . '.php';

        if(!file_exists($controllerFile))
            throw new ParamsNotFoundException('Widget ' . $modulePath . DS . 'Controllers' . DS . $moduleController . ' controller file is not exist');

        // controller class
        $controllerClass = $nameSpace . '\\Controllers\\' . $moduleController;

        // Check for widgets
        if(!class_exists($controllerClass))
            include $controllerFile;

        if(!class_exists($controllerClass))
            throw new ParamsNotFoundException('Widget ' . $controllerClass .' Controller Class is not exists');

        $moduleControllerObject = new $controllerClass;
        $actionMethod           = $action;

        if(!method_exists($moduleControllerObject, $actionMethod))
            throw new ParamsNotFoundException('Widget ' . $actionMethod . ' Action Method is not exists in Controller Class');

        // save current widget data
        $this->currentWidgetRouting = [
            'name'       => $widgetName,
            'modulePath' => $modulePath,
            'module'     => strtolower($module),
            'controller' => strtolower($controller),
            'action'     => $actionMethod,
        ];

        // save loaded widgets data
        $this->loadedWidgetsData[] = $this->currentWidgetRouting;

        return $moduleControllerObject->$actionMethod($params);
    }

    /**
     * @return array
     */
    public function getLoadedWidgetsData(){
        return $this->loadedWidgetsData;
    }

    /**
     * @return array
     */
    public function getCurrentWidgetRouting(){
        return $this->currentWidgetRouting;
    }
}
