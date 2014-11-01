<?php

namespace Safan\GlobalData;

class Cookie
{
    /**
     * @param $name
     * @param $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     * @param null $secure
     * @param bool $httponly
     * @return bool
     */
    public function set($name, $value, $expire=0, $path='/', $domain=null, $secure=null, $httponly=false){
        if(setcookie($name, $value, $expire, $path, $domain, $secure, $httponly))
            return true;
        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function get($name){
        if(isset($_COOKIE[$name]))
            return $_COOKIE[$name];
        return false;
    }

    /**
     * @param $name
     */
    public function remove($name){
        if(isset($_COOKIE[$name])){ $this->set($name, 'false', time() - 1); }
    }
}