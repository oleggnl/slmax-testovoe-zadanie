<?php

class DBConnect {
	protected static $instance = null;
	public $dbh = null;

	private function __construct($host, $usr, $pswd, $db) {
		$this->dbh = new PDO("mysql:host=$host;dbname=$db", $usr, $pswd);
		var_dump($this->dbh);
		if ($this->dbh === null) {
			die("Can't connect to database");
		}
	}	
	public static function init($host, $usr, $pswd, $db) {
		if (self::$instance === null) {
			self::$instance = new self($host, $usr, $pswd, $db);  
		}
 
		return self::$instance;
	}
	public static function getInstance() {
		if (self::$instance === null) {
		    die('please init database instance');
		}
		return self::$instance;
	}
	 
    private function __clone() {
    }

    public  function __wakeup() {
    }
}

