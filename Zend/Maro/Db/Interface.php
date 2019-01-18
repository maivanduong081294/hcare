<?php

interface Maro_Db_Interface
{
    public function insert($query);
    public function delete($data,$where);
    public function update($table, $data,$where);
    public function excute($sql);
    public function select($sql);
    public function select2($sql);
}