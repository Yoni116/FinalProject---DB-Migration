<?php

namespace Neo;
use Connections\Neo4jConnector;
use Mysql\ConversionData;
use Mysql\Table;


require_once __DIR__.'/../Mysql/Table.php';
require_once __DIR__.'/../Mysql/ConversionData.php';

/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/24/2016
 * Time: 9:09 AM
 */
class NeoManager
{


    public function createRelationship($relationInfo)
    {
        $refKeysArray = ConversionData::getRefIdArray();

        $neoClient = Neo4jConnector::getInstance()->getClient();

        $nodeList = $neoClient->makeLabel($relationInfo['SourceNode'])->getNodes();



        foreach($nodeList as $firstNode)
        {
            $firstNodeId = $firstNode->getId();
            $keyPropertyValue = $firstNode->getProperty($relationInfo['SourceName']);
            $keysFound = ConversionData::searchRefArray($relationInfo['DestNode'].$relationInfo['DestName'],$keyPropertyValue);

            if(($firstId = array_search($firstNodeId, $keysFound)) !== false) { // remove the first node from the found keys
                unset($keysFound[$firstId]);
            }

            foreach($keysFound as $key)
            {
                $secondNode = $neoClient->getNode($key);
                $firstNode->relateTo($secondNode,$relationInfo['RelationshipName'])->save();

            }

        }

        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        echo "after creating relationship ". $relationInfo['RelationshipName']. " Process Time: {$time} \n";




    }

// TODO added error handling
    public function createNodes($table)
    {
        $neoClient = Neo4jConnector::getInstance()->getClient();

        $label = $neoClient->makeLabel($table->getTableName());

        $cols = $table->getColumns();

        $numberOfCols = count($cols);

        $tempArray = ConversionData::getKeysToRelationship();
        $arrayOfColToRef = array();
        foreach($tempArray as $data)
        {
            if(!in_array($data['SourceName'],$arrayOfColToRef) && !in_array($data['DestName'],$arrayOfColToRef))
            {
                if ($data['SourceNode'] == $table->getTableName())
                    array_push($arrayOfColToRef, $data['SourceName']);

                else
                    if ($data['DestNode'] == $table->getTableName())
                        array_push($arrayOfColToRef, $data['DestName']);
            }
        }

        $tempArray = ConversionData::getTablesToRelationship();
        foreach($tempArray as $data)
        {
            if(!in_array($data['SourceName'],$arrayOfColToRef) && !in_array($data['DestName'],$arrayOfColToRef))
            {
                if ($data['SourceNode'] == $table->getTableName())
                    array_push($arrayOfColToRef, $data['SourceName']);

                else
                    if ($data['DestNode'] == $table->getTableName())
                        array_push($arrayOfColToRef, $data['DestName']);
            }
        }

//        $colsType = array();

//        for($i = 0; $i < $numberOfCols; $i++)
//        {
//            $type = ConversionData::convertDataType($cols[$i]->getDataType());
//            $name = $cols[$i]->getColName();
//
//            array_push($colsType,array('Name' => $name, 'Type' => $type));
//        }

        $data = $table->getRowsData();
        //$tmpNodes = array();

        //$neoClient->startBatch();
        foreach($data as $row)
        {
            $node = $neoClient->makeNode();
            for($i = 0; $i < $numberOfCols; $i++)
            {
//                $propName = $cols[$i]->getColName();
//
//                $type = ConversionData::convertDataType($cols[$i]->getDataType());

                switch($cols[$i]->getNewType())
                {
                    case "int":
                        $propData = intval($row[$cols[$i]->getColName()]);
                        break;
                    case "long":
                        $propData = floatval(strtotime($row[$cols[$i]->getColName()]));
                        break;
                    case "String":
                        $propData = $row[$cols[$i]->getColName()];
                        break;

                }

                $node->setProperty($cols[$i]->getColName(),$propData);


            }
            $node->save();



            for($i = 0; $i < $numberOfCols; $i++)
            {
                $found = array_search($cols[$i]->getColName(),$arrayOfColToRef);
                if($found !== false )
                    ConversionData::addToRefArray($node->getId(), array($table->getTableName().$cols[$i]->getColName(),$row[$cols[$i]->getColName()]));
            }

          //  print_r(ConversionData::getRefIdArray());

            //array_push($tmpNodes,$node);
            $node->addLabels(array($label));



            //$node->setProperty()
        }

//        $answer = $neoClient->commitBatch();
//
//        if($answer)
//        {
//            foreach ($tmpNodes as $node)
//            {
//                $node->addLabels(array($label));
//            }
//        }

        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        echo "after creating nodes {$table->getTableName()} Process Time: {$time} \n";

    }

}