<?php

namespace Safan\Handler;

use Safan\Safan;

abstract class Handler
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
    public function __construct($config=null){
        Safan::setHandler($this);

        $this->runApplication();
    }

    /**
     *
     */
    public function run($modules){
        $this->modules = $modules;

        $this->handlingProcess();
    }
}