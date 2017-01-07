<?php

namespace Connections;

use \PDO;

/**
 * Class MysqlConnector
 * @package Connections
 */
class MysqlConnector
{

    private static $instance = null; // the main instance for the connection used for singleton
    private $db; // the db connection object it self
    private $stmt; // used in class in order to execute the queries

    /**
     * @param $mysqlConfig [connection info: host, db name, username , password]
     * creates instance for connection
     */
    private function __construct($mysqlConfig)
    {

        try {
            $this->db = new PDO('mysql:host=' . $mysqlConfig['host'] . ';dbname=' . $mysqlConfig['dbname'], $mysqlConfig['username'], $mysqlConfig['password']);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * @return bool
     * always false - singleton
     */
    private function __clone()
    {
        return false;
    }

    /**
     * @param bool|false $mysqlConfig
     * @return MysqlConnector
     * get the singleton instance of connection
     */
    public static function getInstance($mysqlConfig = false)
    {
        if (!self::$instance && $mysqlConfig != false)
            self::$instance = new self($mysqlConfig);

        return self::$instance;
    }

    /**
     * @return mixed
     * DB query to return all table names in DB
     */
    public function getAllTables()
    {
        try {
            $query = ("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
            $this->stmt = $this->db->prepare($query);
            if ($this->stmt->execute()) {
                $final_result['status'] = "OK";
                $final_result['data'] = $this->stmt->fetchAll();
                return $final_result;

            } else {
                $final_result['reason'] = "problem fetching tables list";
                return $final_result;
            }


        } catch (PDOException $e) {
            $this->stmt = null;
            $final_result['status'] = "EXCEPTION";
            $final_result['reason'] = $e->getMessage();
            return $final_result;
        }
    }

    /**
     * @param $tableName
     * @return mixed
     * DB query to get table description and all keys for table
     */
    public function getTableInfo($tableName)
    {
        try {
            $query = ("DESCRIBE " . $tableName);
            $this->stmt = $this->db->prepare($query);

            if ($this->stmt->execute()) {
                $final_result['status'] = "OK";
                $final_result['data'] = $this->stmt->fetchAll();
                return $final_result;
            } else {
                $final_result['reason'] = "problem fetching col list";
                return $final_result;
            }
        } catch (PDOException $e) {
            $this->stmt = null;
            $final_result['status'] = "EXCEPTION";
            $final_result['reason'] = $e->getMessage();
            return $final_result;
        }

    }

    /**
     * @param $tableName
     * @return mixed
     * DB query to get all the raw data of table
     */
    public function getTableData($tableName)
    {
        try {
            $query = ("SELECT * FROM " . $tableName);
            $this->stmt = $this->db->prepare($query);

            if ($this->stmt->execute()) {
                $final_result['status'] = "OK";
                $final_result['data'] = $this->stmt->fetchAll();
                return $final_result;
            } else {
                $final_result['reason'] = "problem fetching data";
                return $final_result;
            }
        } catch (PDOException $e) {
            $this->stmt = null;
            $final_result['status'] = "EXCEPTION";
            $final_result['reason'] = $e->getMessage();
            return $final_result;
        }
    }

    /**
     * @param $dbName
     * @return mixed
     * DB query to get all foreign key connections in DB
     */
    public function getForeignKey($dbName)
    {
        try {
            $query = ("SELECT
                        concat(table_name, '.', column_name) AS 'foreign key',
                        concat(referenced_table_name, '.', referenced_column_name) AS 'references'
                        FROM
                            information_schema.key_column_usage
                        WHERE
                            referenced_table_name IS NOT NULL
                            AND table_schema = :dbName ");
            $this->stmt = $this->db->prepare($query);
            $this->stmt->bindParam(':dbName', $dbName);

            if ($this->stmt->execute()) {
                $final_result['status'] = "OK";
                $final_result['data'] = $this->stmt->fetchAll();
                return $final_result;
            } else {
                $final_result['reason'] = "problem fetching col list";
                return $final_result;
            }
        } catch (PDOException $e) {
            $this->stmt = null;
            $final_result['status'] = "EXCEPTION";
            $final_result['reason'] = $e->getMessage();
            return $final_result;
        }


    }


    //    public function getColData($colName, $tableName)
//    {
//        try {
//            $query = ("SELECT " . $colName . " FROM " . $tableName);
//            $this->stmt = $this->db->prepare($query);
//
//            if ($this->stmt->execute()) {
//                $final_result['status'] = "OK";
//                $final_result['data'] = $this->stmt->fetchAll();
//                return $final_result;
//            } else {
//                $final_result['reason'] = "problem fetching data";
//                return $final_result;
//            }
//        } catch (PDOException $e) {
//            $this->stmt = null;
//            $final_result['status'] = "EXCEPTION";
//            $final_result['reason'] = $e->getMessage();
//            return $final_result;
//        }
//    }

    /**
     * @return bool
     */
    public function __wakeup()
    {
        return false;
    }

    function __destruct()
    {
        $this->stmt = null;
        $this->db = null;
    }
}


