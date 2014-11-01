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

        // get dispatcher
        $dispatcher = Safan::handler()->getObjectManager()->get('dispatcher');

        // load widget
        $dispatcher->loadModule($widget['module'], $widget['controller'], $widget['action'], $params);
	}
}
