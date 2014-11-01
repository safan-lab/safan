<?php

namespace SafanResponse\Commands;

use Safan\CliManager\CliManager;

class ErrorController
{
    /**
     * Get Error message for unknown command
     */
    public function notfoundAction(){
        return CliManager::getErrorMessage('Command not found');
    }
}