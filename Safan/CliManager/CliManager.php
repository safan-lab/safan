<?php

namespace Safan\CliManager;

use Safan\GlobalData\Get;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class CliManager
{
    /**
     * string (example database:create)
     */
    private $command;

    /**
     * @param $command
     */
    public function __construct($command){
        $this->command = $command;

        return $this->dispatchCommand();
    }

    /**
     * @var $command array
     */
    private function dispatchCommand(){
        Safan::handler()->getObjectManager()->get('router')->checkCliCommand($this->command);

        if(Get::exists('module'))
            $module = Get::str('module');
        else
            return $this->getErrorMessage('Module Global Variable is not exists');

        if(Get::exists('controller'))
            $controller = Get::str('controller');
        else
            return $this->getErrorMessage('Controller Global Variable is not exists');

        if(Get::exists('action'))
            $action = Get::str('action');
        else
            return $this->getErrorMessage('Action Global Variable is not exists');

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
            return $this->getErrorMessage($module . ' module or path are not exist');

        // Controller Class Name
        $moduleController = ucfirst(strtolower($controller)) . 'Controller';
        $controllerFile = $modulePath . DS . 'Commands' . DS . $moduleController . '.php';
        $this->currentController = $controller;

        if(!file_exists($controllerFile))
            return $this->getErrorMessage($modulePath . DS . 'Commands' . DS . $moduleController . ' controller file is not exist');

        include $controllerFile;

        // controller class
        $controllerClass = $nameSpace . '\\Commands\\' . $moduleController;

        if(!class_exists($controllerClass))
            return $this->getErrorMessage($controllerClass .' Controller Class is not exist');

        $moduleControllerObject = new $controllerClass;
        $actionMethod = strtolower($action) . 'Action';

        if(!method_exists($moduleControllerObject, $actionMethod))
            return $this->getErrorMessage($actionMethod . ' Action Method is not exist in Controller Class');

        return $moduleControllerObject->$actionMethod();
    }

    /**
     * Get Message
     *
     * @color green
     */
    public static function getMessage($message){
        echo self::setTextColor($message, 'green') . "\n\r";
    }

    /**
     * Get Error
     *
     * @color red
     */
    public static function getErrorMessage($message){
        echo self::setTextColor($message, 'red') . "\n\r";
        exit;
    }

    /**
     * Set Color and return string
     *
     * @param $color
     * @param $str
     * @return string
     */
    public static function setTextColor($str, $color){
        switch ($color) {
            case 'red' :
                $color = "\e[0;31m";
                break;
            case 'yellow' :
                $color = "\e[0;33m";
                break;
            case 'green' :
                $color = "\e[0;32m";
                break;
            case 'blue' :
                $color = "\e[0;34m";
                break;
            case 'white' :
                $color = "\e[0;37m";
                break;
            case 'purple' :
                $color = "\e[0;35m";
                break;
            case 'cyan' :
                $color = "\e[0;36m";
                break;
        }

        return $color . $str . "\e[0m";
    }
}