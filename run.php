<?php
require_once "dbconnect.php";
require_once "person.php";
require_once "persons_list.php";

$host_name = "db_host";
$user_name = "user";
$user_pswd = "123";
$db_name = "testdb";

$dbc = DBConnect::init($host_name, $user_name, $user_pswd, $db_name);

person::initTable();

$pl1 = new persons_list('>= 1');

foreach ($pl1->getPersons() as $p) {
	$r = $p->getUser();
	var_dump($r);
}



$p1 = person::createNew('Vasia', 'Pupkin', '1961-01-01', 0, 'Moscow');
$p2 = person::createNew('Alex', 'Pushkin', '1961-01-02', 0, 'St.Peterburg');
$p3 = person::createNew('Peter', 'First', '1961-01-03', 0, 'Moscow');

$p1->save();
$p2->save();
$p3->save();

$pl2 = new persons_list('>= 1');

foreach ($pl2->getPersons() as $p) {
	$r = $p->getUser();
	var_dump($r);
}

$pl2->deletePersons();
