<?php

require_once 'Connections\MysqlConnector.php';
require_once 'Connections\Neo4jConnector.php';
require_once 'Mysql\MysqlManager.php';
require_once 'Neo4j\NeoManager.php';



// ****** php 5.6 ******
// rough estimation of 1mb data from db to 70mb on ram
// 31.5mb from db took 64 secs

// 147mb from db took 11GB of ram
// and it took around 19min to complete


// *** after some tweaking :
// 4gb memory and 25 secs to pull data



// ****** php 7.0.1 ******
// rough estimation of 1mb data from db to 51mb on ram
// 31.5mb from db took 20 secs

// 147mb from db took 8.2GB of ram
// and it took around 4.5min to complete

// *** after some tweaking :
// 2gb memory and 16 secs to pull data



use Connections\MysqlConnector;
use Connections\Neo4jConnector;
use Mysql\MysqlManager;
use Neo\NeoManager;




static $dbName = 'employees';

//$config = array('host' => 'kokoreko.ddns.net:8888/', 'dbname' => 'testDB', 'username' => 'test', 'password' => '');
$mysqlConfig = array('host' => 'localhost', 'dbname' => $dbName, 'username' => 'testUser', 'password' => '');
$neoConfig = array('host' => 'localhost', 'dbname' => $dbName, 'username' => 'neo4j', 'password' => 'yoni', 'port' => '7474');


$test = new \Mysql\ConversionData();
$mysql = MysqlConnector::getInstance($mysqlConfig);
$neo4j = Neo4jConnector::getInstance($neoConfig);



$mysqlDB = new MysqlManager($dbName);

print_r(\Mysql\ConversionData::getTablesToRelationship());
print_r(\Mysql\ConversionData::getKeysToRelationship());

$neo4j = new NeoManager();


foreach($mysqlDB->getTablesArray() as $table)
{
    if(in_array($table->getTableName(),\Mysql\ConversionData::getTablesToNodes()))
        $neo4j->createNodes($table);
}

$relationshipsArray = \Mysql\ConversionData::getKeysToRelationship();



foreach($relationshipsArray as $relationshipInfo)
{
   $neo4j->createRelationship($relationshipInfo);
}





$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Process Time: {$time}";

