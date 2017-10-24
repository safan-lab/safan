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

use Safan\Handler\BaseHandler;

define('SAFAN_FRAMEWORK_PATH', dirname(__FILE__));

class Safan
{
    /**
     * @var BaseHandler
     */
    private static $_handler;

    /**
     * Create Handler.
     *
     * @param string $class
     * @return BaseHandler
     */
    public static function createHandler(string $class)
    {
        return self::createHttpHandler($class);
    }

    /**
     * Call Http Handler
     *
     * @param string $class
     * @return BaseHandler
     */
    public static function createHttpHandler(string $class): BaseHandler
    {
        return new $class;
    }

    /**
     * Returns the handler singleton.
     *
     * @return BaseHandler
     */
    public static function handler(): BaseHandler
    {
        return self::$_handler;
    }

    /**
     * @param BaseHandler $handler
     */
    public static function setHandler(BaseHandler $handler)
    {
        self::$_handler = $handler;
    }
}