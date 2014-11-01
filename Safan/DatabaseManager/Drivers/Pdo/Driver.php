<?php

namespace Safan\DatabaseManager\Drivers\Pdo;

use Safan\DatabaseManager\Drivers\Pdo\Exceptions\PdoException;
use Safan\DatabaseManager\Drivers\Pdo\Exceptions\ConnectionParamsNotExistsException;
use Safan\DatabaseManager\Drivers\Pdo\Exceptions\NoConnectionException;
use Safan\DatabaseManager\Drivers\Pdo\Exceptions\QueryFailedException;

class Driver{
	
	protected $dbh;
	protected $sth;
	protected $config;
	public $debug=false;
	
	private static $instance=array();
	
	private function __construct(){}
	
	private function __clone(){}
	
	public static function getInstance(){
		$class_name = __CLASS__;
		if(!isset(self::$instance[$class_name]) ){
			self::$instance[$class_name] = new $class_name();
		}
		return self::$instance[$class_name];
	}

    /**
     * @param array $config
     * @throws Exceptions\ConnectionParamsNotExistsException
     */
    public function setup(array $config){
		$dbhost = isset($config['db_host']) ? $config['db_host'] : false;
		$dbname = isset($config['db_name']) ? $config['db_name'] : false;
		$dbuser = isset($config['db_user']) ? $config['db_user'] : false;
		$dbpass = isset($config['db_pass']) ? $config['db_pass'] : false;
	
		if(isset($config['db_debug'])){
			$this->debug = $config['db_debug'];
		}
	
		if(!$dbhost || !$dbname || !$dbuser || !$dbpass)
			throw new ConnectionParamsNotExistsException();
		
		try {
			$this->dbh = new \PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); //cp1251
			$this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
		}
		catch (PDOException $e) {
			if($this->debug === true){
				echo "\r\n<!-- PDO CONNECTION ERROR: ".$e->getMessage()."-->\r\n";
			}
			$this->connect_error = "Error!: " . $e->getMessage() . "<br/>";
			$this->dbh = null;
			return;
		}
	}

    /**
     * @param $query
     * @param array $params
     * @return bool
     * @throws Exceptions\NoConnectionException
     */
    public function query($query, $params=array())
	{
		if (is_null($this->dbh))
			throw new NoConnectionException();
        try{
            $this->sth = $this->dbh->prepare($query);
            if($this->sth->execute($params))
            	return $this->debug = false;
            return $this->debug = true;
        }
        catch (PDOException $e){
            return false;
        }
	}

    /**
     * @return mixed
     * @throws Exceptions\QueryFailedException
     */
    public function selectAll()
	{
		if(is_null($this->sth))
			throw new QueryFailedException();	
		return $this->sth->fetchAll(\PDO::FETCH_OBJ);
	}

    /**
     * @return null
     * @throws Exceptions\QueryFailedException
     */
    public function selectOnce()
	{
		if(is_null($this->sth))
			throw new QueryFailedException();
		$result = $this->sth->fetch(\PDO::FETCH_OBJ);
		if($result)
			return $result;
		return null;
	}

    /**
     * @return bool
     * @throws Exceptions\QueryFailedException
     */
    public function insert()
	{
		if(is_null($this->sth))
			throw new QueryFailedException();
		return ($this->dbh->lastInsertId() > 0) ? $this->dbh->lastInsertId() : false;
	}

    /**
     * @return bool
     * @throws Exceptions\QueryFailedException
     */
    public function update()
	{
		if(is_null($this->sth))
			throw new QueryFailedException();
		if($this->sth->rowCount() > 0)
			return $this->sth->rowCount();
		return false; 
	}

    /**
     * @return bool
     * @throws Exceptions\QueryFailedException
     */
    public function delete()
	{
		if(is_null($this->sth))
			throw new QueryFailedException();
		if($this->debug === true)
			return false;
		return true;
	}
	
}
