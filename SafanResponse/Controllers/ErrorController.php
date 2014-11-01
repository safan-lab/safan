<?php
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