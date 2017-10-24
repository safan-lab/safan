<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Router;

use Safan\GlobalData\Get;
use Safan\Loader\SplClassLoader;
use Safan\Safan;

class Router
{
    /**
     * @var array
     */
    private $httpRoutes = [];

    /**
     * @var array
     */
    private $cliRoutes = [];

    /**
     * Check http routes for module
     */
    public function checkHttpRoutes()
    {
        // get modules
        $modules = Safan::handler()->getModules();

        $frameworkDefaultRoute = [];

        // get all routes
        foreach ($modules as $moduleName => $modulePath) {
            $routerFile = APP_BASE_PATH . DS . $modulePath . DS . 'Resources' . DS . 'config' . DS . 'router.config.php';

            if (file_exists($routerFile)) {
                $route = include($routerFile);

                if ($moduleName != 'SafanResponse') {
                    $this->httpRoutes = array_replace($this->httpRoutes, $route);
                } else {
                    $frameworkDefaultRoute = $route;
                }
            }
        }

        // merge default route
        foreach ($frameworkDefaultRoute as $route => $routeParam) {
            if (!isset($this->httpRoutes[$route])) {
                $this->httpRoutes[$route] = $routeParam;
            }
        }
    }

    /**
     * Check cli routes for module
     */
    public function checkCliRoutes()
    {
        // get modules
        $modules = Safan::handler()->getModules();

        // get all routes
        foreach ($modules as $moduleName => $modulePath) {
            $routerFile = APP_BASE_PATH . DS . $modulePath . DS . 'Resources' . DS . 'config' . DS . 'cli.router.config.php';

            if (file_exists($routerFile)) {
                $route           = include($routerFile);
                $this->cliRoutes = array_merge($this->cliRoutes, $route);
            }
        }
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function checkUri(string $uri = ''): bool
    {
        $isMatch = false;

        foreach ($this->httpRoutes as $rule => $settings) {
            $matches = [];

            if (preg_match($rule, $uri, $matches)) {
                Get::setParams('module', $settings['module']);
                Get::setParams('controller', $settings['controller']);
                Get::setParams('action', $settings['action']);

                $route['matches'] = [];

                foreach ($settings['matches'] as $key => $varName) {
                    if (empty($varName)) {
                        continue;
                    }

                    if (isset($matches[$key])) {
                        $_GET[$varName] = $matches[$key];
                    }
                }

                return $isMatch = true;
            }
        }

        if (!$isMatch) {
            $this->checkUri('/404');
        }
    }

    /**
     * @param $command
     * @return bool
     */
    public function checkCliCommand(string $command)
    {
        $isMatch        = false;
        $matchedCommand = [];

        foreach ($this->cliRoutes as $rule => $settings) {
            $matches = [];

            if (preg_match($rule, $command, $matches)) {
                if (isset($settings['important']) && $settings['important']) {
                    $matchedCommand = [
                        'command' => $this->cliRoutes[$rule],
                        'matches' => $matches
                    ];

                    break;
                } else {
                    $matchedCommand = [
                        'command' => $this->cliRoutes[$rule],
                        'matches' => $matches
                    ];
                }

                $isMatch = true;
            }
        }
        
        if (!$isMatch) {
            $this->checkCliCommand('/404');
        } else {
            $this->selectCommand($matchedCommand['command'], $matchedCommand['matches']);
        }
    }

    /**
     * @param string $command
     * @param array $matches
     */
    private function selectCommand(string $command, array $matches = [])
    {
        Get::setParams('module', $command['module']);
        Get::setParams('controller', $command['controller']);
        Get::setParams('action', $command['action']);

        $route['matches'] = [];

        foreach ($command['matches'] as $key => $varName) {
            if (empty($varName)) {
                continue;
            }

            if (isset($matches[$key])){
                $_GET[$varName] = $matches[$key];
            }
        }
    }
}
