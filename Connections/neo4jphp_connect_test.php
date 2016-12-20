<?php
namespace Connections;

require('../vendor/autoload.php'); // or your custom autoloader

use Everyman;
// Connecting to the default port 7474 on localhost



// Connecting using HTTPS and Basic Auth
$client = new Everyman\Neo4j\Client();
$client->getTransport()
->setAuth('neo4j', 'yoni');

// Test connection to server
print_r($client->getServerInfo());