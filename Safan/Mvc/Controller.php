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
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class Controller
{
    /**
     * Page Title
     *
     * @var string
     */
    public $pageTitle = 'Safan Framework';

    /**
     * Meta keywords for page
     *
     * @var string
     */
    public $keywords = 'PHP, framework, application for all needs';

    /**
     * Description for page
     *
     * @var string
     */
    public $description = 'Safan - Simple application or all needs';

    /**
     * Vars for extract
     *
     * @var array
     */
    private $vars = [];

    /**
     * Assign Vars
     *
     * @param string $key
     * @param $value
     */
    public function assign(string $key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * @param  string $layout
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function setLayout(string $layout)
    {
        $layoutPaths = explode(':', $layout);

        if (sizeof($layoutPaths) !== 2) {
            throw new FileNotFoundException('Layout is not correct');
        }

        $moduleName     = $layoutPaths[0];
        $layoutFileName = $layoutPaths[1];

        $modules = Safan::handler()->getModules();

        if (!isset($modules[$moduleName])) {
            throw new FileNotFoundException('Layout module is not define');
        }

        $layoutFile = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Layouts' . DS . $layoutFileName . '.php';

        if (!file_exists($layoutFile)) {
            throw new FileNotFoundException('Layout '. $layoutFile .' is not exist');
        }

        Safan::handler()->getObjectManager()->get('view')->setLayoutFile($layoutFile);
    }

    /**
     * @param string $view
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function render(string $view)
    {
        // generate view file
        $modulePath     = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath();
        $controllerName = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentController();
        $viewFile       = $modulePath . DS . 'Resources' . DS . 'view' . DS . strtolower($controllerName) . DS . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new FileNotFoundException($viewFile . ' View file not found');
        }

        // set data
        Safan::handler()->getObjectManager()->get('view')->pageTitle   = $this->pageTitle;
        Safan::handler()->getObjectManager()->get('view')->keywords    = $this->keywords;
        Safan::handler()->getObjectManager()->get('view')->description = $this->description;

        Safan::handler()->getObjectManager()->get('view')->setViewFile($viewFile);
        Safan::handler()->getObjectManager()->get('view')->loadViewFile($this->vars);
        Safan::handler()->getObjectManager()->get('view')->loadLayoutFile($this->vars);
    }

    /**
     * @param string $view
     * @return mixed
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function renderPartial(string $view)
    {
        $modulePath     = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentModulePath();
        $controllerName = Safan::handler()->getObjectManager()->get('dispatcher')->getCurrentController();
        $view           = $modulePath . DS . 'Resources' . DS . 'view' . DS . strtolower($controllerName) . DS . $view . '.php';

        if (!file_exists($view)) {
            throw new FileNotFoundException($view . ' View file not found');
        }

        return Safan::handler()->getObjectManager()->get('view')->loadWidgetFile($view, $this->vars);
    }

    /**
     * @param $view
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     */
    protected function renderWidget($view){
        $widgetRouting  = Safan::handler()->getObjectManager()->get('widget')->getCurrentWidgetRouting();
        $modulePath     = $widgetRouting['modulePath'];
        $controllerName = $widgetRouting['controller'];
        $view           = $modulePath . DS . 'Resources' . DS . 'view' . DS . strtolower($controllerName) . DS . $view . '.php';

        if(!file_exists($view))
            throw new ParamsNotFoundException($widgetRouting['name'] . ' Widget view file not found');

        return Safan::handler()->getObjectManager()->get('view')->loadWidgetFile($view, $this->vars);
    }

    /**
     * Render Json Content
     *
     * @param array $params
     */
    public function renderJson(array $params = [])
    {
        echo json_encode($params);
        return;
    }

    /**
     * Redirect
     *
     * @param string $url
     * @param bool $globalUrl
     */
    public function redirect(string $url = '', bool $globalUrl = false)
    {
        if ($globalUrl) {
            header('location: ' . $url);
            exit;
        }

        if (!$url) {
            header('location: ' . Safan::handler()->baseUrl);
        } else {
            header('location: ' . Safan::handler()->baseUrl . $url);
        }

        exit;
    }
}
