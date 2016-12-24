<?php

namespace Connections;

use \PDO;

class MysqlConnector
{

    private static $instance = null;
    private $db;
    private $stmt;

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

    public function __clone()
    {
        return false;
    }

    public function __wakeup()
    {
        return false;
    }

//close database connection
    function __destruct()
    {
        $this->stmt = null;
        $this->db = null;
    }


    public static function getInstance($mysqlConfig = false)
    {
        if (!self::$instance && $mysqlConfig != false)
            self::$instance = new self($mysqlConfig);

        return self::$instance;
    }


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
}

