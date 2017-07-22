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