<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\CacheManager;

class MemcacheManager
{
    /**
     * @var \Memcache
     */
    private $memcache;

    /**
     * MemcacheManager constructor.
     */
    public function __construct()
    {
        $this->memcache = new \Memcache;
        $this->memcache->connect('localhost', 11211) or die ("Could not connect");
    }

    /**
     * @param string $key
     * @return array|bool|string
     */
    public function get(string $key)
    {
        return ($this->memcache) ? $this->memcache->get($key) : false;
    }

    /**
     * Set Memcache
     *
     * @param string $key
     * @param $object
     * @param int $timeout
     * @return bool
     */
    public function set(string $key, $object, $timeout = 60)
    {
        return ($this->memcache) ? $this->memcache->set($key, $object, MEMCACHE_COMPRESSED, $timeout) : false;
    }

    /**
     * Remove Memcache key
     *
     * @param string $key
     * @return bool
     */
    public function remove(string $key)
    {
        return ($this->memcache) ? $this->memcache->delete($key) : false;
    }
}