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

class Post
{
    /**
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset($_POST[$name]);
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function str(string $name, $default = '')
    {
        return $_POST[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param int $default
     * @return int
     */
    public static function int(string $name, $default = 0)
    {
        return isset($_POST[$name]) ? (int)$_POST[$name] : $default;
    }

    /**
     * @param string $name
     * @param array $default
     * @return array
     */
    public static function strArr(string $name, $default = [])
    {
        if (!isset($_POST[$name]) || !is_array($_POST[$name])) {
            return $default;
        }

        return $_POST[$name];
    }

    /**
     * @param string $name
     * @param array $default
     * @return array
     */
    public static function intArr(string $name, $default = [])
    {
        if (!isset($_POST[$name]) || !is_array($_POST[$name])) {
            return $default;
        }

        return array_map(function($v) {
            return (int)$v;
        }, $_POST[$name]);
    }

    /**
     * @param string $name
     * @param bool $default
     * @return bool
     */
    public static function bool(string $name, $default = false)
    {
        return isset($_POST[$name]) ? (bool)$_POST[$name] : $default;
    }

    /**
     * @param string $name
     * @param float $default
     * @return float
     */
    public static function float(string $name, $default = 0.0)
    {
        return isset($_POST[$name]) ? (float)$_POST[$name] : $default;
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function setParams(string $key, $value)
    {
        $_POST[$key] = $value;
    }
}