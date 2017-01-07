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
    private static $keysToRelationship = array();
    private static $refIdArray = array();

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
     * @return array
     */
    public static function getForeignKeyList()
    {
        return self::$foreignKeyList;
    }

    /**
     * @return array
     */
    public static function getTablesToRelationship()
    {
        return self::$tablesToRelationship;
    }

    /**
     * @return array
     */
    public static function getKeysToRelationship()
    {
        return self::$keysToRelationship;
    }

    /**
     * @return array
     */
    public static function getRefIdArray()
    {
        return self::$refIdArray;
    }

    /**
     * @return array
     */
    public static function getTablesToNodes()
    {
        return self::$tablesToNodes;
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
        if (strpos($dataType, 'char') !== false || strpos($dataType, 'text') !== false) {
           return "String";
        }
        if (strpos($dataType, 'int') !== false || strpos($dataType, 'binary') !== false) {
            return "int";
        }
        if (strpos($dataType, 'date') !== false || strpos($dataType, 'time') !== false) {
            return "long";
        }
        if (strpos($dataType, 'double') !== false) {
            return "long";
        }

        return "String";

    }

    public static function analyzeKeys($tablesName)
    {
        $tmpArray = ConversionData::$foreignKeyList;

        $priKeys = ConversionData::$primaryKeyList;
        $first = array_pop($tmpArray);

        $found = false;

        while($first != null) {

            $i = 0;
            foreach ($tmpArray as $keyEntry) {
                if ($first['SourceTable'] == $keyEntry['SourceTable'])
                {
                    $firstFound = false;
                    $secondFound = false;
                    foreach($priKeys as $priKeyInfo)
                    {
                        if($priKeyInfo['TableName'] == $first['SourceTable'])
                        {
                            if($first['SourceColumn'] == $priKeyInfo['ColumnName'])
                                $firstFound = true;
                            if($keyEntry['SourceColumn'] == $priKeyInfo['ColumnName'])
                                $secondFound = true;
                        }
                    }
                    if($firstFound && $secondFound)
                    {
                        array_push(ConversionData::$tablesToRelationship, array('SourceNode' => $first['DestTable'], 'DestNode' => $keyEntry['DestTable'], 'SourceName' => $first['DestColumn'], 'DestName' => $keyEntry['DestColumn'], 'RelationshipName' => $first['SourceTable'], 'SourceColumnSource' => $first['SourceColumn'] , 'DestColumnSource' => $keyEntry['SourceColumn'] ));
                        unset($tmpArray[$i--]);

                        unset($tablesName[array_search($first['SourceTable'], $tablesName)]);
                        $found = true;
                    }
                }
                $i++;
            }
            if(!$found)
                array_push(ConversionData::$keysToRelationship,array('SourceNode' => $first['SourceTable'],'DestNode' => $first['DestTable'],'SourceName' => $first['SourceColumn'], 'DestName' => $first['DestColumn'] , 'RelationshipName' => $first['SourceTable']." To ".$first['DestTable']));

            $first = array_pop($tmpArray);

            $found = false;

        }

        foreach($tablesName as $name)
        {
            array_push(ConversionData::$tablesToNodes,$name);
        }

    }

    public static function addToRefArray($fkey,$key,$value)
    {
        ConversionData::$refIdArray[$fkey][$key]=$value;
    }

    public static function searchRefArray($fkey,$value)
    {
        $arr = array();

        foreach(ConversionData::$refIdArray[$fkey] as $nodeId => $arrValue)
        {
            //if(array_key_exists($fkey,$subArray) && $subArray[$fkey] == $value)
            if($arrValue == $value)
                array_push($arr,$nodeId);
        }

        return $arr;
    }
    public static function searchRefArrayById($id,$fkey)
    {
        return ConversionData::$refIdArray[$fkey][$id];

    }


}