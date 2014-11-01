<?php

namespace Safan\Router;

use Safan\GlobalData\Get;
use Safan\Loader\SplClassLoader;
use Safan\Safan;

class Router
{
    /**
     * @var array
     */
    private $httpRoutes = array();

    /**
     * @var array
     */
    private $cliRoutes = array();

    /**
     * Check http routes for module
     */
    public function checkHttpRoutes(){
        // get modules
        $modules = Safan::handler()->getModules();
        // get all routes
        foreach($modules as $moduleName => $modulePath){
            $routerFile = APP_BASE_PATH . DS . $modulePath . DS . 'Resources' . DS . 'config' . DS . 'router.config.php';

            // register module namespaces
            $loader = new SplClassLoader($moduleName, $modulePath . DS . '..' . DS);
            $loader->register();

            if(file_exists($routerFile)){
                $route = include($routerFile);
                $this->httpRoutes = array_merge($this->httpRoutes, $route);
            }
        }
    }

    /**
     * Check cli routes for module
     */
    public function checkCliRoutes(){
        // get modules
        $modules = Safan::handler()->getModules();
        $modules = array_reverse($modules);
        // get all routes
        foreach($modules as $moduleName => $modulePath){
            $routerFile = APP_BASE_PATH . DS . $modulePath . DS . 'Resources' . DS . 'config' . DS . 'cli.router.config.php';

            // register module namespaces
            $loader = new SplClassLoader($moduleName, $modulePath . DS . '..' . DS);
            $loader->register();

            if(file_exists($routerFile)){
                $route = include($routerFile);
                $this->cliRoutes = array_merge($this->cliRoutes, $route);
            }
        }
    }

    /**
     * @param $uri
     * @return bool
     */
    public function checkUri($uri){
        $isMatch = false;

        foreach ($this->httpRoutes as $rule => $settings) {
            $matches = array();

            if (preg_match($rule, $uri, $matches)) {
                Get::setParams('module', $settings['module']);
                Get::setParams('controller', $settings['controller']);
                Get::setParams('action', $settings['action']);

                $route['matches'] = array();
                foreach ($settings['matches'] as $key => $varName) {
                    if (empty($varName))
                        continue;
                    if (isset($matches[$key])){
                        $_GET[$varName] = $matches[$key];
                    }
                }
                $isMatch = true;
                return true;
            }
        }

        if(!$isMatch)
            $this->checkUri('/404');
    }

    /**
     * @param $command
     * @return bool
     */
    public function checkCliCommand($command){
        $isMatch = false;

        foreach ($this->cliRoutes as $rule => $settings) {
            $matches = array();

            if (preg_match($rule, $command, $matches)) {
                Get::setParams('module', $settings['module']);
                Get::setParams('controller', $settings['controller']);
                Get::setParams('action', $settings['action']);

                $route['matches'] = array();
                foreach ($settings['matches'] as $key => $varName) {
                    if (empty($varName))
                        continue;
                    if (isset($matches[$key])){
                        $_GET[$varName] = $matches[$key];
                    }
                }
                $isMatch = true;
                return true;
            }
        }


        if(!$isMatch){
            $this->checkCliCommand('/404');}
    }

}
