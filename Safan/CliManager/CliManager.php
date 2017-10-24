<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\CliManager;

use Safan\GlobalData\Get;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class CliManager
{
    /**
     * string (example database:create)
     *
     * @var string
     */
    private $command;

    /**
     * @param $command
     */
    public function __construct($command)
    {
        $this->command = $command;

        return $this->dispatchCommand();
    }

    /**
     * dispatch command
     */
    private function dispatchCommand()
    {
        Safan::handler()->getObjectManager()->get('router')->checkCliCommand($this->command);

        if (Get::str('module', false)) {
            $module = Get::str('module');
        } else {
            return $this->getErrorMessage('Module Global Variable is not exists');
        }

        if (Get::str('controller', false)) {
            $controller = Get::str('controller');
        } else {
            return $this->getErrorMessage('Controller Global Variable is not exists');
        }

        if (Get::str('action', false)) {
            $action = Get::str('action');
        } else {
            return $this->getErrorMessage('Action Global Variable is not exists');
        }

        // get all modules
        $modules = Safan::handler()->getModules();

        if (isset($modules[$module]) && is_dir(APP_BASE_PATH . DS . $modules[$module])) {
            $nameSpace = '\\' . $module;
            $this->currentModulePath = $modulePath = APP_BASE_PATH . DS . $modules[$module];
        } elseif (isset($modules[ucfirst(strtolower($module))]) &&
                  is_dir(APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))])
        ){ // check case sensitivity
            $nameSpace = '\\' . ucfirst(strtolower($module));
            $this->currentModulePath = $modulePath = APP_BASE_PATH . DS . $modules[ucfirst(strtolower($module))];
        } else {
            return $this->getErrorMessage($module . ' module or path are not exist');
        }

        // Controller Class Name
        $moduleController        = ucfirst(strtolower($controller)) . 'Controller';
        $controllerFile          = $modulePath . DS . 'Commands' . DS . $moduleController . '.php';

        if (!file_exists($controllerFile)) {
            return $this->getErrorMessage($modulePath . DS . 'Commands' . DS . $moduleController . ' controller file is not exist');
        }

        include $controllerFile;

        // controller class
        $controllerClass = $nameSpace . '\\Commands\\' . $moduleController;

        if (!class_exists($controllerClass)) {
            return $this->getErrorMessage($controllerClass .' Controller Class is not exist');
        }

        $moduleControllerObject = new $controllerClass;
        $actionMethod           = strtolower($action) . 'Action';

        if (!method_exists($moduleControllerObject, $actionMethod)) {
            return $this->getErrorMessage($actionMethod . ' Action Method is not exist in Controller Class');
        }

        return $moduleControllerObject->$actionMethod();
    }

    /**
     * Get Message
     *
     * @color green
     * @param string $message
     */
    public static function getMessage(string $message)
    {
        echo self::setTextColor($message, 'green') . "\n\r";
    }

    /**
     * Get Error
     *
     * @color red
     * @param string $message
     */
    public static function getErrorMessage(string $message)
    {
        echo self::setTextColor($message, 'red') . "\n\r";
        exit;
    }

    /**
     * Set Color and return string
     *
     * @param string $str
     * @param string $color
     * @return string
     */
    public static function setTextColor(string $str, string $color)
    {
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