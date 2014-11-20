<?php

namespace Safan\Handler;

use Safan\Assets\AssetManager;
use Safan\Dispatcher\Dispatcher;
use Safan\EventListener\EventListener;
use Safan\FileStorageManager\FileStorageManager;
use Safan\FlashMessenger\FlashMessenger;
use Safan\GlobalData\Cookie;
use Safan\GlobalData\Session;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Logger\Logger;
use Safan\ObjectManager\ObjectManager;
use Safan\Request\Request;
use Safan\Router\Router;
use Safan\WidgetManager\WidgetManager;

class HttpHandler extends Handler
{
    /**
     * Object Manager instance
     */
    private $objectManager;

    /**
     * @var boolean
     */
    private $debugMode;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @return array
     */
    public function getModules(){
       return $this->modules;
    }

    /**
     * @return mixed
     */
    public function getObjectManager(){
        return $this->objectManager;
    }

    /**
     * @return mixed
     */
    public function getDebugMode(){
        return $this->debugMode;
    }

    /**
     * @return mixed
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * Set Base Url
     */
    private function setBaseUrl($url = false){
        if(isset($_SERVER['HTTP_HOST'])){
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            if($url && $url != "")
                $this->baseUrl = $protocol . $_SERVER['HTTP_HOST'] . '/' . $url;
            else
                $this->baseUrl = $protocol . $_SERVER['HTTP_HOST'];

            $this->getObjectManager()->get('cookie')->set('m_ref', $this->baseUrl);
        }
    }

    /**
     *
     */
    public function runApplication(){
        /****************** Main Config ************************/
        $mainConfigFile = APP_BASE_PATH . DS . 'application' . DS . 'Settings' . DS . 'main.config.php';
        if(file_exists($mainConfigFile))
            $config = include($mainConfigFile);
        else
            throw new FileNotFoundException('Main Config file "'. $mainConfigFile .'" not found');

        /****************** Set Debug mode *******************/
        if(isset($config['debug']) && $config['debug'] === true)
            $this->debugMode = true;
        else
            $this->debugMode = false;

        /******************* Object manager ***************/
        $this->objectManager = $om = new ObjectManager();

        /******************* Event listener ***************/
        if(!isset($config['events']))
            $config['events'] = array();
        $eventListener = new EventListener($config['events']);
        $om->setObject('eventListener', $eventListener);

        /******************* Dispatcher object ************/
        $dispatcher = new Dispatcher();
        $om->setObject('dispatcher', $dispatcher);

        /******************* Router object ************/
        $router = new Router();
        $om->setObject('router', $router);

        /******************* Request object ************/
        if(!defined('INTERFACE_TYPE')){
            $request = new Request();
            $om->setObject('request', $request);
        }

        /******************* Session Object ***************/
        if(!defined('INTERFACE_TYPE')){
            $session = new Session();
            $session->start();
            $om->setObject('session', $session);
        }

        /******************* Cookie Object ****************/
        if(!defined('INTERFACE_TYPE')){
            $cookie = new Cookie();
            $om->setObject('cookie', $cookie);
        }

        /******************* Logger object **********/
        $logger = new Logger();
        $om->setObject('logger', $logger);

        /******************* FileStorage Object ************/
        if(!class_exists('Imagick'))
            $om->get('logger')->setLog('imagick', 'Imagick class not found');
        $fileStorage = new FileStorageManager();
        $om->setObject('fileStorage', $fileStorage);

        /******************* FlashMessenger Object ********/
        if(!defined('INTERFACE_TYPE')){
            $flashMessenger = new FlashMessenger();
            $om->setObject('flashMessenger', $flashMessenger);
        }

        /******************* WidgetManager Object *********/
        $widgetManager = new WidgetManager();
        $om->setObject('widget', $widgetManager);

        /******************* Set Base url *********************/
        if(isset($config['base_url']))
            $this->setBaseUrl($config['base_url']);
        else
            $this->setBaseUrl(null);

        /******************* Assets Object *********/
        if(!isset($config['assets_path']))
            throw new ParamsNotFoundException('Assets path is not defined');
        $assetManager = new AssetManager($config['assets_path']);
        $om->setObject('assets', $assetManager);

        unset($config);
        unset($om);
    }

    /**
     * @return mixed|void
     */
    protected function handlingProcess(){
        $this->objectManager->get('eventListener')->runEvent('preCheckRoute');
        $this->objectManager->get('router')->checkHttpRoutes();
        $this->objectManager->get('eventListener')->runEvent('postCheckRoute');

        $this->objectManager->get('eventListener')->runEvent('preDispatch');
        $this->objectManager->get('dispatcher')->dispatch();
        $this->objectManager->get('eventListener')->runEvent('postDispatch');
    }
}