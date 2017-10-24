<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Safan\Handler;

use Safan\CacheManager\MemcacheManager;
use Safan\Dispatcher\Dispatcher;
use Safan\EventListener\EventListener;
use Safan\FlashMessenger\FlashMessenger;
use Safan\GlobalData\Cookie;
use Safan\GlobalData\Session;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Logger\Logger;
use Safan\Mvc\View;
use Safan\ObjectManager\ObjectManager;
use Safan\Request\Request;
use Safan\Router\Router;
use Safan\WidgetManager\WidgetManager;

class HttpHandler extends BaseHandler
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var boolean
     */
    private $debugMode;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @return array
     */
    public function getModules(): array
    {
       return $this->modules;
    }

    /**
     * @param string $namespace
     * @param string $path
     */
    public function addModule(string $namespace, string $path)
    {
        $this->modules[$namespace] = $path;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @return bool
     */
    public function getDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * Set Base Url
     *
     * @param bool $url
     * @param bool $initProtocol
     */
    private function setBaseUrl($url = false, $initProtocol = false)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            if ($initProtocol)
                $this->protocol = $initProtocol;
            else
                $this->protocol = $protocol;

            if ($url && $url != "")
                $this->baseUrl = $this->protocol . $_SERVER['HTTP_HOST'] . '/' . $url;
            else
                $this->baseUrl = $this->protocol . $_SERVER['HTTP_HOST'];

            $this->getObjectManager()->get('cookie')->set('m_ref', $this->baseUrl);
        }
    }

    /**
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function runApplication()
    {
        /****************** Config files ************************/
        $localConfigFile = APP_BASE_PATH . DS . 'application' . DS . 'Settings' . DS . 'local.config.php';
        $mainConfigFile  = APP_BASE_PATH . DS . 'application' . DS . 'Settings' . DS . 'main.config.php';

        if (!file_exists($mainConfigFile)) {
            throw new FileNotFoundException('Main Config file "'. $mainConfigFile .'" not found');
        }

        if (file_exists($localConfigFile)) {
            $config = include $localConfigFile;
        } else {
            $config = include $mainConfigFile;
        }

        $this->config = $config;

        /****************** Set Debug mode *******************/
        if (isset($config['debug']) && $config['debug'] === true) {
            $this->debugMode = true;
        } else {
            $this->debugMode = false;
        }

        $this->setDebugMode();

        /******************* Object manager ***************/
        $this->objectManager = $om = new ObjectManager();

        /******************* Event listener ***************/
        if (!isset($config['events'])) {
            $config['events'] = array();
        }

        $eventListener = new EventListener($config['events']);
        $om->setObject('eventListener', $eventListener);

        /******************* Memcache ***************/
        if (isset($config['memcache']) && $config['memcache']) {
            $memcache = new MemcacheManager();
            $om->setObject('memcache', $memcache);
        }

        /******************* Dispatcher object ************/
        $dispatcher = new Dispatcher();
        $om->setObject('dispatcher', $dispatcher);

        /******************* Router object ************/
        $router = new Router();
        $om->setObject('router', $router);

        /******************* Request object ************/
        if (!defined('INTERFACE_TYPE')) {
            $request = new Request();
            $om->setObject('request', $request);
        }

        /******************* Session Object ***************/
        if (!defined('INTERFACE_TYPE')) {
            $session = new Session();
            $session->start();
            $om->setObject('session', $session);
        }

        /******************* Cookie Object ****************/
        $cookie = new Cookie();
        $om->setObject('cookie', $cookie);

        /******************* Logger object **********/
        $logger = new Logger();
        $om->setObject('logger', $logger);

        /******************* FlashMessenger Object ********/
        if (!defined('INTERFACE_TYPE')) {
            $flashMessenger = new FlashMessenger();
            $om->setObject('flashMessenger', $flashMessenger);
        }

        /******************* View manager *****************/
        $view = new View();
        $om->setObject('view', $view);

        /******************* WidgetManager Object *********/
        $widgetManager = new WidgetManager();
        $om->setObject('widget', $widgetManager);

        /******************* Set Base url *********************/
        $initProtocol = false;

        if (isset($config['protocol'])) {
            $initProtocol = $config['protocol'];
        }

        if (isset($config['base_url'])) {
            $this->setBaseUrl($config['base_url'], $initProtocol);
        } else {
            $this->setBaseUrl(null, $initProtocol);
        }

        unset($config);
        unset($om);
    }

    /**
     * Initialize libraries from config
     *
     * @return bool
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    protected function initLibraries()
    {
        if (empty($this->config['init'])) {
            return false;
        }

        foreach ($this->config['init'] as $lib) {
            if (!isset($lib['class']) || !isset($lib['method'])) {
                throw new ParamsNotFoundException('Initialization library is not correct');
            }

            if (class_exists($lib['class'])) {
                $dataMapper = new $lib['class'];
            } else {
                throw new FileNotFoundException($lib['class']);
            }

            if (method_exists($dataMapper, $lib['method'])) {
                if (isset($lib['params'])) {
                    $dataMapper->{$lib['method']}($lib['params']);
                } else {
                    $dataMapper->{$lib['method']}();
                }
            } else {
                throw new ParamsNotFoundException($lib['method']);
            }
        }
    }

    /**
     * Set Debug Mode
     */
    private function setDebugMode()
    {
        if ($this->debugMode) {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
    }

    /**
     * @return mixed|void
     */
    protected function handlingProcess()
    {
        $this->objectManager->get('eventListener')->runEvent('preCheckRoute');
        $this->objectManager->get('router')->checkHttpRoutes();
        $this->objectManager->get('eventListener')->runEvent('postCheckRoute');

        // initialize libraries from config
        $this->initLibraries();

        $this->objectManager->get('eventListener')->runEvent('preDispatch');
        $this->objectManager->get('dispatcher')->dispatch();
        $this->objectManager->get('eventListener')->runEvent('postDispatch');
    }
}