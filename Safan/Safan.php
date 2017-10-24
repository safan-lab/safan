<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Safan;

class Safan
{
    /**
     * Handler instance
     *
     * @var
     */
    private static $_handler;

    /**
     * Create Handler
     *
     * @param $class
     * @return mixed
     */
    public static function createHandler($class)
    {
        return self::createHttpHandler($class);
    }

    /**
     * Call Http Handler
     *
     * @param $class
     * @return mixed
     */
    public static function createHttpHandler($class)
    {
        return new $class;
    }

    /**
     * Returns the handler singleton, null if the singleton has not been created yet.
     *
     * @return mixed
     */
    public static function handler()
    {
        return self::$_handler;
    }

    /**
     * @param $handler
     * @throws GlobalExceptions\FileNotFoundException
     */
    public static function setHandler($handler)
    {
        self::$_handler = $handler;
    }
}