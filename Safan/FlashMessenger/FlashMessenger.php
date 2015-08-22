<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\FlashMessenger;

use Safan\Safan;

class FlashMessenger
{
    /**
     * @var string
     */
    private $sessionNameSpace = 'FlashMessenger';
    /**
     * @var
     */
    private $sessionObject;

    /**
     *
     */
    public function __construct(){
        if(is_null($this->sessionObject))
            $this->sessionObject = Safan::handler()->getObjectManager()->get('session');
    }

    /**
     * @param $key
     * @return bool
     */
    public function get($key){
        $flashSessions = $this->sessionObject->get('FlashMessenger');
        if($flashSessions && isset($flashSessions[$key]))
            return $flashSessions[$key];
        return false;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value){
        $this->sessionObject->set($this->sessionNameSpace, array($key=>$value));
    }

    /**
     * @param $key
     */
    public function remove($key){
        $this->sessionObject->remove($this->sessionNameSpace);
    }
}