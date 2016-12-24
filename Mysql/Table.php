<?php
namespace Mysql;


require_once 'Column.php';
require_once 'ConversionData.php';


use Connections\MysqlConnector;




/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/10/2016
 * Time: 5:06 PM
 */


class Table
{
    private $tableName = "";
    private $numOfColumns = 0;
    private $columns = array();
    private $rowsData = array();



    public function __construct($name)
    {
        $this->tableName = $name;
        $this->getColInfo();
        $this->getTableData();
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRowsData()
    {
        return $this->rowsData;
    }





    function getColInfo()
    {
        $mysql = MysqlConnector::getInstance();

        $result = $mysql->getTableInfo($this->tableName);
        foreach($result['data'] as $col)
        {
            if($col['Key'] != "" )
            {
                ConversionData::addKey($this->tableName, $col['Field'], $col['Key'], $col['Extra']);
            }
            array_push($this->columns,new Column($this->tableName,$col['Field'],$col['Type'],$col['Null'],$col['Key'],$col['Default'],$col['Extra']));
            ++$this->numOfColumns;
        }


    }

    function getTableData()
    {
        $mysql = MysqlConnector::getInstance();

        $result = $mysql->getTableData($this->tableName);
        //array_push($this->rowsData,$result['data']);
        $this->rowsData = $result['data'];
        //print_r($this->rowsData);
    }

    //function getTableRelationShip


//    public function addColumn($colName, $dateType)
//    {
//        array_push($this->columns,new Column($colName, $dateType));
//        ++$this->numOfColumns;
//    }

}