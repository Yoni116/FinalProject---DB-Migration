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
    private static $tablesToNodes = array();
    private static $tablesToRelationship = array();

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

    public static function convertDataType($dataType)
    {
        if (strpos($dataType, 'char') !== false) {
           return "String";
        }
        if (strpos($dataType, 'int') !== false) {
            return "int";
        }
        if (strpos($dataType, 'date') !== false) {
            return "long";
        }

    }

    public static function analyzeKeys($tablesName)
    {
        $tmpArray = ConversionData::$foreignKeyList;


        $first = array_pop($tmpArray);

        while($first != null) {

            $i = 0;
            foreach ($tmpArray as $keyEntry) {
                if ($first['SourceTable'] == $keyEntry['SourceTable'])
                {
                    array_push(ConversionData::$tablesToRelationship,array('SourceNode' => $first['DestTable'],'DestNode' => $keyEntry['DestTable'],'SourceName' => $first['DestColumn'], 'DestName' => $keyEntry['DestColumn'] , 'RelationshipName' => $first['SourceTable']));
                    unset($tmpArray[$i--]);

                    unset($tablesName[array_search($first['SourceTable'],$tablesName)]);

                }
                $i++;
            }

            $first = array_pop($tmpArray);

        }

        foreach($tablesName as $name)
        {
            array_push(ConversionData::$tablesToNodes,$name);
        }






    }


}