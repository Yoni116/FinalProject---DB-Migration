<?php

require_once 'Connections\MysqlConnector.php';
require_once 'Mysql\MysqlManager.php';

// ****** php 5.6 ******
// rough estimation of 1mb data from db to 70mb on ram
// 31.5mb from db took 64 secs

// 147mb from db took 11GB of ram
// and it took around 19min to complete


// ****** php 7.0.1 ******
// rough estimation of 1mb data from db to 51mb on ram
// 31.5mb from db took 20 secs

// 147mb from db took 8.2GB of ram
// and it took around 4.5min to complete
use Connections\MysqlConnector;
use Mysql\MysqlManager;



static $dbName = 'employees';

//$config = array('host' => 'kokoreko.ddns.net:8888/', 'dbname' => 'testDB', 'username' => 'test', 'password' => '');
$config = array('host' => 'localhost', 'dbname' => $dbName, 'username' => 'testUser', 'password' => '');

$test = new \Mysql\ConversionData();
$mysql = MysqlConnector::getInstance($config);

$mysqlDB = new MysqlManager($dbName);

var_dump($test);

$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Process Time: {$time}";