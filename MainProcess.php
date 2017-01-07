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

///////////////////// sakila DB ////////////
// Last runtime : 2 hours
// size of DB on neo4j = 7.4 MB
// Nodes = 40811
// Properties = 78944
// Relationships = 115307

// Last runtime : 2:10 hours
// size of DB on neo4j = 6.4 MB
// Nodes = 40811
// Properties = 52515
// Relationships = 115307

// Last runtime : 20 Minutes
// size of DB on neo4j = 6.4 MB
// Nodes = 40811
// Properties = 52371
// Relationships = 116307

///////////////////// controly DB ////////////
// Last runtime : 45 Seconds
// size of DB on neo4j = less then 100 KB
// Nodes = 2811
// Properties = 4980
// Relationships = 1475

///////////////////// world DB ////////////
// Last runtime : 2 Minutes
// size of DB on neo4j = around 100 KB
// Nodes = 5302
// Properties = 9438
// Relationships = 5063




use Connections\MysqlConnector;
use Connections\Neo4jConnector;
use Mysql\MysqlManager;
use Neo\NeoManager;




static $dbName = 'sakila';

//$config = array('host' => 'kokoreko.ddns.net:8888/', 'dbname' => 'testDB', 'username' => 'test', 'password' => '');
$mysqlConfig = array('host' => 'localhost', 'dbname' => $dbName, 'username' => 'testUser', 'password' => '');
$neoConfig = array('host' => 'localhost', 'dbname' => $dbName, 'username' => 'neo4j', 'password' => 'yoni', 'port' => '7474');


$test = new \Mysql\ConversionData();
$mysql = MysqlConnector::getInstance($mysqlConfig);
$neo4j = Neo4jConnector::getInstance($neoConfig);



$mysqlDB = new MysqlManager($dbName);

print_r("PrimaryKeys List:");
print_r(\Mysql\ConversionData::getPrimaryKeyList());
print_r("ForeignKeys List:");
print_r(\Mysql\ConversionData::getForeignKeyList());
print_r("TablesToRelationship List:");
print_r(\Mysql\ConversionData::getTablesToRelationship());
print_r("KeysToRelationship List:");
print_r(\Mysql\ConversionData::getKeysToRelationship());

$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "after scanning schema Process Time: {$time} \n";

$neo4j = new NeoManager();

//$neo4j->createNodes($mysqlDB->getTablesArray()[14]);
//$neo4j->createNodes($mysqlDB->getTablesArray()[15]);
//
//$neo4j->createRelationship(\Mysql\ConversionData::getKeysToRelationship()[0]);

// full working loops on all data
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

$tableRelationshipsArray = \Mysql\ConversionData::getTablesToRelationship();

foreach($tableRelationshipsArray as $relationshipInfo)
{
    $neo4j->createRelationshipFromTable($relationshipInfo,$mysqlDB->getTableByName($relationshipInfo['RelationshipName']));
}


$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Process Time: {$time}";

