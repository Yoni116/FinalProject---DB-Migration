<?php

namespace Connections;

require('vendor/autoload.php');
use Everyman\Neo4j;

class Neo4jConnector
{

    private static $instance = null; // the main instance for the connection used for singleton
    private $client; // the db connection object it self


    /**
     * @param $neoConfig [connection info: host, port, username , password]
     * creates instance for connection
     */
    private function __construct($neoConfig)
    {
        try
        {
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

    /**
     * @return bool
     */
    public function __clone()
    {
        return false;
    }

    /**
     * @param bool|false $neoConfig
     * @return Neo4jConnector|null
     * get the singleton instance of connection
     */
    public static function getInstance($neoConfig = false)
    {
        if (!self::$instance && $neoConfig != false)
            self::$instance = new self($neoConfig);

        return self::$instance;
    }

    /**
     * used to check if the connection to server is valid
     */
    public function printServerInfo()
    {
        print_r($this->client->getServerInfo());
    }



    public function getClient()
    {
        return $this->client;
    }
    //close database connection
    function __destruct()
    {

    }

}