<?php

namespace Neo;
use Connections\Neo4jConnector;
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





    public function createNodes($table)
    {
        $neoClient = Neo4jConnector::getInstance()->getClient();

        $label = $neoClient->makeLabel($table->getTableName());

        $cols = $table->getColumns();

        $numberOfCols = count($cols);

        $data = $table->getRowsData();

        foreach($data as $row)
        {
            $node = $neoClient->makeNode();
            for($i = 0; $i < $numberOfCols; $i++)
            {
                $propName = $cols[$i]->getColName();
                $propDate =$row[$propName];

                $node->setProperty($propName,$propDate);
            }
            $node->save();
            $node->addLabels(array($label));

            //$node->setProperty()
        }



    }

}