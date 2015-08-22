<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SafanResponse\Controllers;

class ErrorController extends \Safan\Mvc\Controller{

    /**
     * 404 page, Not found
     */
    public function error404Action(){
        header('HTTP/1.0 404 Not Found');

        return $this->render('404');
    }
}