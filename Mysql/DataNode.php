<?php

/**
 * Created by IntelliJ IDEA.
 * User: yoni
 * Date: 12/17/2016
 * Time: 12:34 PM
 */

namespace Mysql;


class DataNode
{
    private $rowNum;
    private $data;


    public function __construct($rowNum, $data)
    {
        $this->rowNum = $rowNum;
        $this->data = $data;
    }


}