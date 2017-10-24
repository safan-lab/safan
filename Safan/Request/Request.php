<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\Request;

use Safan\GlobalData\Get;
use Safan\Safan;

class Request
{
    /**
     * @var string
     */
    private $requestType = '';

    /**
     * @return bool
     */
    public function isPostRequest(){
        if($_SERVER['REQUEST_METHOD'] == 'POST')
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isGetRequest(){
        if($_SERVER['REQUEST_METHOD'] == 'GET')
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isDeleteRequest(){
        if($_SERVER['REQUEST_METHOD'] == 'DELETE')
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isPutRequest(){
        if($_SERVER['REQUEST_METHOD'] == 'PUT')
            return true;
        return false;
    }

    /**
     * @return string
     */
    public function getRequestType(){
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'PUT':
                $this->requestType = 'PUT';
                break;
            case 'POST':
                $this->requestType = 'POST';
                break;
            case 'GET':
                $this->requestType = 'GET';
                break;
            case 'DELETE':
                $this->requestType = 'DELETE';
                break;
        }

        return $this->requestType;
    }

    /**
     * @return string
     */
    public function getUri(){
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $uri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $uriRequest = strpos($uri, Safan::handler()->baseUrl);
        if($uriRequest !== false){
            $uri = substr($uri, strlen(Safan::handler()->baseUrl) - $uriRequest);
        }

        //Get Variables
        if (strpos($uri, '?') !== false){
            $uriVars = parse_str(substr(strstr($uri, '?'), 1), $outPutVars);
            //Generate Get variables
            foreach($outPutVars as $key => $value){
                if(($key != 'module') && ($key != 'controller') && ($key != 'action'))
                    Get::setParams($key, $value);
            }
            //Generate main uri
            $uri = strstr($uri, '?', true);
        }

        return $uri;
    }
}