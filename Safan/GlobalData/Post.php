<?php

namespace Safan\GlobalData;

class Post
{
    /**
     * @param $name
     * @param bool $default
     * @return bool|string
     */
    public static function exists($name, $default = false)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    /**
     * @param $name
     * @param string $default
     * @return string
     */
    public static function str($name, $default = '')
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    /**
     * @param $name
     * @param int $default
     * @return int
     */
    public static function int($name, $default = 0)
    {
        return isset($_POST[$name]) ? (int)$_POST[$name] : $default;
    }

    /**
     * @param $name
     * @param array $default
     * @return array
     */
    public static function strArr($name, $default = array())
    {
        if (!isset($_POST[$name]) || !is_array($_POST[$name]))
            return $default;
        else
            return $_POST[$name];
    }

    /**
     * @param $name
     * @param array $default
     * @return array
     */
    public static function intArr($name, $default = array())
    {
        if (!isset($_POST[$name]) || !is_array($_POST[$name]))
            return $default;
        else
            return array_map(function($v){ return (int)$v; }, $_POST[$name]);
    }

    /**
     * @param $name
     * @param bool $default
     * @return bool
     */
    public static function bool($name, $default = false)
    {
        return isset($_POST[$name]) ? (bool)$_POST[$name] : $default;
    }

    /**
     * @param $name
     * @param float $default
     * @return float
     */
    public static function float($name, $default = 0.0)
    {
        return isset($_POST[$name]) ? (float)$_POST[$name] : $default;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function setParams($key, $value)
    {
        $_POST[$key] = $value;
    }
}