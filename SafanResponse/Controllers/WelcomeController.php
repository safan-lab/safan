<?php
namespace SafanResponse\Controllers;

class WelcomeController extends \Safan\Mvc\Controller{

    /**
     * Welcome page
     */
    public function indexAction(){
        return $this->render('index');
    }

    /**
     * Widget
     */
    public function widgetAction($params = array()){
        $this->renderPartial('widget');
    }
}