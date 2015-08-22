<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\EventListener;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ObjectDoesntExistsException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class EventListener
{
    /**
     * @var array
     */
    private $events = array();

    /**
     *
     */
    public function __construct($events = array()){
        $this->events = $events;
    }

    /**
     * @param $eventKey
     * @return bool
     * @throws \Safan\GlobalExceptions\ObjectDoesntExistsException
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function runEvent($eventKey){
        if(!isset($this->events[$eventKey]))
            return false;

        $eventStr = $this->events[$eventKey];
        $event = explode(':', $eventStr);

        if(sizeof($event) != 2)
            throw new ParamsNotFoundException('Event params is not correct - ' . $eventStr);

        $moduleName = $event[0];
        $eventClass = $event[1];

        // get all modules
        $allModules = Safan::handler()->getModules();

        // check Module
        if(!isset($allModules[$moduleName]))
            throw new FileNotFoundException($event[0] . ' Module is not defined');

        $eventFile = $allModules[$moduleName] . DS . 'Events' . DS . $eventClass . '.php';
        $eventClass = $moduleName . '\\Events\\' . $eventClass;

        // check Event file
        if(!file_exists($eventFile))
            throw new FileNotFoundException($eventFile . ' event file is not exist');

        // check event class for double call
        if(!class_exists($eventClass))
            include $eventFile;

        // check event class
        if(!class_exists($eventClass))
            throw new ObjectDoesntExistsException($eventClass . ' is not defined');

        $eventObj = new $eventClass;

        // check init method
        if(!method_exists($eventObj, 'init'))
            throw new ObjectDoesntExistsException($eventClass . ' have not method init()');

        return $eventObj->init();
    }
}