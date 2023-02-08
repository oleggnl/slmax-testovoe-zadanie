<?php

class person {
	protected int $id = 0;
	protected ?string $first_name;
	protected ?string $last_name;
	protected ?string $birth_at;
	protected int $sex;
	protected ?string $birth_city;
	protected static ?string $_table_name = 'persons';

	public function __construct($id, $first_name, $last_name, $birth_at, $sex, $birth_city) {
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->birth_at = $birth_at;
		$this->sex = $sex;
		$this->birth_city = $birth_city;
		$this->id = $id;
	}
	
	public function save() {
		$dbc = DBConnect::getInstance();
		
		$instance = null;
		$presult = false;
		if ($dbc != null) {
			if ($this->id == 0) {
				$sth = $dbc->dbh->prepare("INSERT INTO ".self::$_table_name." (first_name, last_name, birth_at, sex, birth_city) VALUES (:first_name, :last_name, :birth_at, :sex, :birth_city)");
				$sth->bindParam('first_name', $this->first_name, PDO::PARAM_STR);
				$sth->bindParam('last_name', $this->last_name, PDO::PARAM_STR);
				$sth->bindParam('birth_at', $this->birth_at, PDO::PARAM_STR);
				$sth->bindParam('sex', $this->sex, PDO::PARAM_STR);
				$sth->bindParam('birth_city', $this->birth_city, PDO::PARAM_STR);
			} else {
				$sth = $dbc->dbh->prepare("UPDATE ".self::$_table_name." SET first_name=:first_name, last_name=:last_name, birth_at=:birth_at, sex=:sex, birth_city=:birth_city WHERE id=:id");
				$sth->bindParam('first_name', $this->first_name, PDO::PARAM_STR);
				$sth->bindParam('last_name', $this->last_name, PDO::PARAM_STR);
				$sth->bindParam('birth_at', $this->birth_at, PDO::PARAM_STR);
				$sth->bindParam('sex', $this->sex, PDO::PARAM_STR);
				$sth->bindParam('birth_city', $this->birth_city, PDO::PARAM_STR);
				$sth->bindParam('id', $this->id, PDO::PARAM_INT);
			}
			//$sth->debugDumpParams();
			$presult = $sth->execute();
			if ($this->id == 0) {
				$this->id = $dbc->dbh->lastInsertId();
				echo "new id $this->id\n";
			}
		}
		return $presult;
	}
	public function delete() {
		if ($this->id == 0) {
			return true;
		}
		$dbc = DBConnect::getInstance();
		
		$presult = false;
		if ($dbc != null) {
			$sth = $dbc->dbh->prepare("DELETE FROM ".self::$_table_name." WHERE id=:id");
			$sth->bindParam('id', $this->id, PDO::PARAM_INT);
			$presult = $sth->execute();
			echo "deleted {$this->id} with result $presult\n";
		}
		return $presult;
	}
	public function getUser(): stdClass
    	{
        	$instance = new stdClass();
        	$instance->id = $this->id;
        	$instance->first_name = $this->first_name;
        	$instance->last_name = $this->last_name;
        	$instance->birth_at = self::getAge($this->birth_at);
        	$instance->sex = self::getSex($this->sex);
        	$instance->birth_city = $this->birth_city;
		return $instance;
	}
	
	public static function getAge(string $birth_at): int
	{
		$date1 = new DateTime($birth_at);
		$date2 = new DateTime();

		return $date1->diff($date2)->format("%y");
	}

	public static function getSex(int $sex): string
	{
		return $sex === 0 ? 'M' : 'W';
	}
	
	public static function createNew($first_name, $last_name, $birth_at, $sex, $birth_city) {
		if (preg_match("/^[a-zA-Z]+$/", $first_name) && preg_match("/^[a-zA-Z]+$/", $last_name)) {
			$instance = new self(0, $first_name, $last_name, $birth_at, $sex, $birth_city);
		} else {
			$instance = null;
		}
		return $instance;
	}
	public static function loadById($id) {
		$dbc = DBConnect::getInstance();
		
		$instance = null;
		if ($dbc != null) {
			$st = $dbc->dbh->prepare("SELECT id, first_name, last_name, birth_at, sex, birth_city FROM ".self::$_table_name." WHERE id=:id");
			if ($st->execute(['id' => $id])) {
				$row = $st->fetch();
				$instance = new self($row['id'], $row['first_name'], $row['last_name'], $row['birth_at'], $row['sex'], $row['birth_city']);
			}
		}
		return $instance;
	}
	public static function createLoaded($id, $first_name, $last_name, $birth_at, $sex, $birth_city) {
		$instance = new self($id, $first_name, $last_name, $birth_at, $sex, $birth_city);
		return $instance;
	}
	public static function getTableName() {
		return self::$_table_name;
	}

	public static function initTable() {
		$dbc = DBConnect::getInstance();
		if ($dbc != null) {
			$q = <<<QUERY
CREATE TABLE IF NOT EXISTS persons (
	id INTEGER AUTO_INCREMENT PRIMARY KEY,
	first_name varchar(100) NOT NULL,
	last_name varchar(100) NOT NULL,
	birth_at DATE,
	sex ENUM('0', '1') NOT NULL,
	birth_city varchar(100) NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci
AUTO_INCREMENT=1;
QUERY;
			if ($dbc->dbh->exec($q) === false) {
				die("Can't initialize table in specified database");
			}
			return true;
		}
	}
}
