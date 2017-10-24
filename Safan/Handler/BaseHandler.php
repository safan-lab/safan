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

use Safan\Safan;

abstract class BaseHandler
{
    /**
     * @return mixed
     */
    abstract protected function handlingProcess();

    /**
     * @var array
     */
    protected $modules;

    /**
     * @param null $config
     */
    public function __construct($config = null)
    {
        Safan::setHandler($this);

        $this->runApplication();
    }

    /**
     * @param array $modules
     */
    public function run(array $modules)
    {
        $this->modules = $modules;

        $this->handlingProcess();
    }
}