<?php
/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/17/2016
 * Time: 8:06 PM
 */

namespace Mysql;

require_once 'Table.php';
require_once 'ConversionData.php';


use Connections\MysqlConnector;


class MysqlManager
{
    public $dbName;


    private $tablesArray = array();
    private $mysql = null;
    /**
     * MysqlManager constructor.
     * @param $dbName
     */
    public function __construct($dbName)
    {
        $this->dbName = $dbName;
        $this->mysql = MysqlConnector::getInstance();
        $this->getTables();
    }

    public function getTables(){



        $result = $this->mysql->getAllTables();
//        $i=0;

        foreach($result['data'] as $tableName)
        {
//            $i++;
            array_push($this->tablesArray, new Table($tableName['Tables_in_'.$this->dbName]));
//            if($i == 4)
//                break;
        }

        $this->getForeignKeyInfo();

    }

    private function getForeignKeyInfo()
    {
        $result = $this->mysql->getForeignKey($this->dbName);

        foreach($result['data'] as $fkInfo)
        {
            $source = explode('.',$fkInfo['foreign key']);
            $dest = explode('.', $fkInfo['references']);

            ConversionData::addForeignKey($source[0],$source[1],$dest[0],$dest[1]);
        }

    }

}