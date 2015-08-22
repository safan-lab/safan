<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Handler;

use Safan\CliManager\CliManager;

class ConsoleHandler extends HttpHandler
{
    /**
     * Run Http applications
     */
    public function runApplication(){
        parent::runApplication();
    }

    /**
     * @return CliManager|void
     */
    public function handlingProcess(){
        // Set Environments
        $env = $_SERVER['argv'];

        if(sizeof($env) != 2 || !strpos($env[1], ":"))
            return CliManager::getErrorMessage("Unknown Command \nview help:commands");

        $this->getObjectManager()->get('eventListener')->runEvent('preCliCheckRoute');
        $this->getObjectManager()->get('router')->checkCliRoutes();
        $this->getObjectManager()->get('eventListener')->runEvent('postCliCheckRoute');
        // initialize libraries from config
        $this->initLibraries();

        return new CliManager($env[1]);
    }
}