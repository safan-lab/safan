<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\GlobalData;

class Session
{
    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var bool
     */
    private $sessionStarted = false;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->sessionKey = 'MpSessionKey_' . str_replace('.', '_', $_SERVER['HTTP_HOST']);
    }

    /**
     * start session
     */
    public function start()
    {
        if (!$this->sessionStarted) {
            $this->sessionStarted = true;
            session_start();
        }
    }

    /**
     * @param string $name
     * @param $value
     * @return bool
     */
    public function set(string $name, $value)
    {
        if ($this->sessionStarted) {
            $_SESSION[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function get(string $name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return false;
    }

    /**
     * @param string $name
     */
    public function remove(string $name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * End session
     */
    public function endSession()
    {
        $this->sessionStarted = false;
        session_unset();
        session_destroy();
    }
}
