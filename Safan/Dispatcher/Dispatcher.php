<?php

/*
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Dispatcher;

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Safan\GlobalData\Get;
use Safan\Safan;

class Dispatcher
{
    /**
     * @var string
     */
    private $currentModulePath = '';

    /**
     * @var string
     */
    private $currentController = '';

    /**
     * @return string
     */
    public function getCurrentModulePath(){
        return $this->currentModulePath;
    }

    /**
     * @return string
     */
    public function getCurrentController(){
        return $this->currentController;
    }

    /**
     * Dispatch
     */
    public function dispatch(){
        // check request and set params
        $uri = Safan::handler()->getObjectManager()->get('request')->getUri();
        Safan::handler()->getObjectManager()->get('router')->checkUri($uri);

        $module = Get::exists('module');
        $controller = Get::exists('controller');
        $action = Get::exists('action');

        if(!$module)
            return $this->dispatchToError(404, 'Module Global Variable is not exists');
        if(!$controller)
            return $this->dispatchToError(404, 'Controller Global Variable is not exists');
        if(!$action)
            return $this->dispatchToError(404, 'Action Global Variable is not exists');

        $this->loadModule($module, $controller, $action);
    }

    /**
     * @param $code
     * @param $message
     * @return mixed
     */
    public function dispatchToError($code, $message){
        if(Safan::handler()->getDebugMode())
            Safan::handler()->getObjectManager()->get('flashMessenger')->set('error', $message);
        Safan::handler()->getObjectManager()->get('router')->checkUri('/' . $code);

        $this->loadModule(Get::str('module'), Get::str('controller'), Get::str('action'));
        exit;
    }

    /**
     * Load module
     *
     * @param $module
     * @param $controller
     * @param $action
     * @param array $params
     * @return mixed
     */
    public function loadModule($module, $controller, $action, $params = array()){
        // get all modules
        $modules = Safan::handler()->getModules();

        if(isset($modules[$module]) && is_dir(APP_BASE_PATH . DS . $modules[$module])){
            $nameSpace = '\\' . $module;
            $this->currentModulePath = $modulePath = APP_BASE_PATH . DS . $modules[$module];
        }
        elseif(isset($modules[ucfirst(strtolower($module))]) && is_dir(APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))])){ // check case sensitivity
            $nameSpace = '\\' . ucfirst(strtolower($module));
            $this->currentModulePath = $modulePath = APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))];
        }
        else
            return $this->dispatchToError(404, $module . ' module or path are not exist');

        // Controller Class Name
        $moduleController = ucfirst(strtolower($controller)) . 'Controller';
        $controllerFile = $modulePath . DS . 'Controllers' . DS . $moduleController . '.php';
        $this->currentController = $controller;

        if(!file_exists($controllerFile))
            return $this->dispatchToError(404, $modulePath . DS . 'Controllers' . DS . $moduleController . ' controller file is not exist');

        // controller class
        $controllerClass = $nameSpace . '\\Controllers\\' . $moduleController;

        // Check for widgets
        if(!class_exists($controllerClass))
            include $controllerFile;

        if(!class_exists($controllerClass))
            return $this->dispatchToError(404, $controllerClass .' Controller Class is not exists');

        $moduleControllerObject = new $controllerClass;
        $actionMethod = strtolower($action) . 'Action';

        if(!method_exists($moduleControllerObject, $actionMethod))
            return $this->dispatchToError(404, $actionMethod . ' Action Method is not exists in Controller Class');

        if(!empty($params))
            return $moduleControllerObject->$actionMethod($params);
        else
            return $moduleControllerObject->$actionMethod();
    }
}