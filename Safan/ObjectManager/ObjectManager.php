<?php

/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Safan\ObjectManager;

use Safan\GlobalExceptions\ObjectDoesntExistsException;

class ObjectManager
{
    /**
     * @var array
     */
    public $registry = array();

    /**
     * @var array
     */
    private $initializers = array();

    /**
     * @var array
     */
    private $shareds = array();

    /**
     * @param $name
     * @return mixed
     * @throws \Safan\GlobalExceptions\ObjectDoesntExistsException
     */
    public function get($name)
	{
		if (!isset($this->shareds[$name]))
			throw new ObjectDoesntExistsException(
					sprintf('Object %s doesn\'t exists in the object manager registry', $name));
	
		if ($this->shareds[$name] && isset($this->registry[$name])) {
			return $this->registry[$name];
		}
	
		if (isset($this->initializers[$name])) {
			$this->registry[$name] = call_user_func($this->initializers[$name], $this);
			return $this->registry[$name];
		}
			
		throw new ObjectDoesntExistsException(
				sprintf('Object %s doesn\'t exists in object manager registry', $name));
	}

    /**
     * @param $fullName
     * @return mixed
     * @throws \Safan\GlobalExceptions\ObjectDoesntExistsException
     */
    public function getInstance($fullName)
    {
        if (isset($this->shareds[$fullName]) && isset($this->registry[$fullName]))
            return $this->registry[$fullName];

        if(class_exists($fullName)){
            $object = new $fullName;
            $this->setObject($fullName, $object);

            return $object;
        }

        throw new ObjectDoesntExistsException(
            sprintf('Object %s doesn\'t exists in object manager registry', $name));
    }

    /**
     * @param $name
     * @param $initializer
     * @param bool $isShared
     */
    public function setInitializer($name, $initializer, $isShared = true)
	{
		$this->initializers[$name] = $initializer;
		$this->shareds[$name]      = $isShared;
	}

    /**
     * @param $name
     * @param $object
     * @param bool $isShared
     */
    public function setObject($name, $object, $isShared = true)
	{
		$this->registry[$name] = $object;
		$this->shareds[$name]  = $isShared;
	}
}