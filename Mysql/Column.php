<?php
namespace Mysql;

/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/17/2016
 * Time: 12:10 PM
 */


use Connections\MysqlConnector;

class Column
{
    private $myTableName;
    private $colName;
    private $dataType;
    private $isNull;
    private $key;
    private $default;
    private $extra;


    /**
     * Column constructor.
     * @param $myTableName
     * @param $colName
     * @param $dataType
     * @param $isNull
     * @param $key
     * @param $default
     * @param $extra
     */
    public function __construct($myTableName, $colName, $dataType, $isNull, $key, $default, $extra)
    {
        $this->myTableName = $myTableName;
        $this->colName = $colName;
        $this->dataType = $dataType;
        $this->isNull = $isNull;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getColName()
    {
        return $this->colName;
    }





}