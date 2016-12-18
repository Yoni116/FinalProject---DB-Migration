<?php

namespace Connections;

class Neo4jConnector
{

    private static $instance = null;
    private $db;

    private function __construct($config)
    {


        try {
            self::$db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
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
        self::$stmt = null;
        self::$db = null;
    }


    public static function getInstance($config)
    {
        if (!self::$instance)
            self::$instance = new self($config);

        return self::$instance;
    }

    public function getDB()
    {
        return $this->db;
    }
}