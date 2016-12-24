<?php

namespace Connections;

require('vendor/autoload.php');
use Everyman\Neo4j;

class Neo4jConnector
{

    private static $instance = null;
    private $client;
    private $dbName;

    private function __construct($neoConfig)
    {
        try
        {
            $this->dbName = $neoConfig['dbname'];
            $this->client = new Neo4j\Client($neoConfig['host'], $neoConfig['port']);
            $this->client->getTransport()->setAuth($neoConfig['username'], $neoConfig['password']);
            $this->client->getServerInfo(); // used to check connection
        }

        catch (Neo4j\Exception $e)
        {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    }

    private function __clone()
    {
    }

//close database connection
    function __destruct()
    {

    }


    public static function getInstance($neoConfig = false)
    {
        if (!self::$instance && $neoConfig != false)
            self::$instance = new self($neoConfig);

        return self::$instance;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function printServerInfo()
    {
        print_r($this->client->getServerInfo());
    }
}