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

class Cookie
{
    /**
     * @param $name
     * @param $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     * @param null $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function set($name, $value, $expire = 0, $path = '/', $domain = null, $secure = null, $httpOnly = false)
    {
        if (setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly)) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }

        return false;
    }

    /**
     * @param $name
     */
    public function remove(string $name)
    {
        if (isset($_COOKIE[$name])) {
            $this->set($name, 'false', time() - 1);
        }
    }
}