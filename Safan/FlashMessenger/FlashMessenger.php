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
     * @var \Safan\GlobalData\Session
     */
    private $sessionObject;

    /**
     * FlashMessenger constructor.
     */
    public function __construct()
    {
        if (is_null($this->sessionObject)) {
            $this->sessionObject = Safan::handler()->getObjectManager()->get('session');
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function get(string $key)
    {
        $flashSessions = $this->sessionObject->get('FlashMessenger');
        $this->remove();

        if ($flashSessions && isset($flashSessions[$key])) {
            return $flashSessions[$key];
        }

        return false;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->sessionObject->set($this->sessionNameSpace, [$key => $value]);
    }

    /**
     * remove flash message
     */
    public function remove()
    {
        $this->sessionObject->remove($this->sessionNameSpace);
    }
}