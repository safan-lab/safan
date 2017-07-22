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

class Get
{
    /**
     * @param $name
     * @param bool $default
     * @return bool|string
     */
    public static function exists($name, $default = false)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function str($name, $default = '')
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * @param $name
     * @param int $default
     * @return int
     */
    public static function int($name, $default = 0)
    {
        return isset($_GET[$name]) ? (int)$_GET[$name] : $default;
    }

    /**
     * @param $name
     * @param array $default
     * @return array
     */
    public static function strArr($name, $default = array())
    {
        if (!isset($_GET[$name]) || !is_array($_GET[$name]))
            return $default;
        else
            return $_GET[$name];
    }

    /**
     * @param $name
     * @param array $default
     * @return array
     */
    public static function intArr($name, $default = array())
    {
        if (!isset($_GET[$name]) || !is_array($_GET[$name]))
            return $default;
        else
            return array_map(function($v){ return (int)$v; }, $_GET[$name]);
    }

    /**
     * @param $name
     * @param bool $default
     * @return bool
     */
    public static function bool($name, $default = false)
    {
        return isset($_GET[$name]) ? (bool)$_GET[$name] : $default;
    }

    /**
     * @param $name
     * @param float $default
     * @return float
     */
    public static function float($name, $default = 0.0)
    {
        return isset($_GET[$name]) ? (float)$_GET[$name] : $default;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function setParams($key, $value)
    {
        $_GET[$key] = $value;
    }
}