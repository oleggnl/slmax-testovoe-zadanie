<?php
//require_once "person.php";

if (!class_exists("person")) {
	die('class person is not exists');
}

class persons_list {
	protected array $ids = [];
	public function __construct($condition) {
		$dbc = DBConnect::getInstance();
		if ($dbc == null) {
			throw new Exception('no connection');
		}
		if (preg_match("/^(>|<|>=|<=|!=|=)\s*(\d+)$/", $condition, $array)) {
			$tn = person::getTableName();
			$sth = $dbc->dbh->prepare("SELECT id FROM $tn WHERE id $array[1] $array[2] ORDER BY id");
			if ($sth->execute()) {
				foreach ($sth->fetchAll() as $row) {
					$this->ids[] = $row['id'];
				}
			}
		} else {
			throw new Exception('error in condition');
		}
	}
	
	public function getPersons(): array {
		$presult = [];
		foreach($this->ids as $id) {
			$presult[] = person::loadById($id);
		}
		return $presult; 
	}
	
	public function deletePersons() {
		$presult = $this->getPersons();
		foreach($presult as $p) {
			$p->delete();
		} 
	}

}
