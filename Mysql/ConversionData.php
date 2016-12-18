<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/17/2016
 * Time: 3:43 PM
 */
namespace Mysql;

class ConversionData
{
    private static $primaryKeyList = array();
    private static $foreignKeyList = array();
    private static $indexKeyList = array();
    private static $uniqueKeyList = array();

    public function __construct()
    {

    }

    /**
     * @return array
     */
    public static function getPrimaryKeyList()
    {
        return self::$primaryKeyList;
    }

    /**
     * @param array $primaryKeyList
     */
    public static function setPrimaryKeyList($primaryKeyList)
    {
        self::$primaryKeyList = $primaryKeyList;
    }

    /**
     * @return array
     */
    public static function getForeignKeyList()
    {
        return self::$foreignKeyList;
    }

    /**
     * @param array $foreignKeyList
     */
    public static function setForeignKeyList($foreignKeyList)
    {
        self::$foreignKeyList = $foreignKeyList;
    }




    public static function addKey($tableName,$columnName,$type,$extra)
    {
        switch($type){
            case "PRI":
                array_push(ConversionData::$primaryKeyList,array('TableName' => $tableName , 'ColumnName' => $columnName, 'ExtraInfo' => $extra));
                break;
            case "MUL":
                array_push(ConversionData::$indexKeyList,array('TableName' => $tableName , 'ColumnName' => $columnName, 'ExtraInfo' => $extra));
                break;
            case "UNI":
                array_push(ConversionData::$uniqueKeyList,array('TableName' => $tableName , 'ColumnName' => $columnName, 'ExtraInfo' => $extra));
                break;
        }

    }

    /**
     * @param $tableName
     * @param $columnName
     * @param $destinationTableName
     * @param $destinationTableColumn
     */
    public static function addForeignKey($tableName,$columnName,$destinationTableName,$destinationTableColumn)
    {
        array_push(ConversionData::$foreignKeyList,array('SourceTable' => $tableName , 'SourceColumn' => $columnName, 'DestTable' => $destinationTableName, 'DestColumn' => $destinationTableColumn ));

    }


}