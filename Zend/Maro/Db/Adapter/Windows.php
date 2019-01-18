<?php

class Maro_Db_Adapter_Windows implements Maro_Db_Interface {

    private $_db;

    function __construct() {
        try {
            $DB = "FAST_HNAM_DBTG";
            $serverName = ".\SQLEXPRESS";
            $userName = "";
            $password = "";
            $this->_db = new PDO(
                    "sqlsrv:server=$serverName;Database=$DB", "$userName", "$password"
            );
            return $this->_db;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function delete($data, $where) {
        
    }

    public function excute($sql) {
        $stmt = $this->_db->prepare($sql);
        $result = $stmt->execute();
        return $result;
    }

    public function insert($sql) {
        
    }

    public function update($table, $data, $where) {
        
    }

    public function select($sql) {
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function select2($sql) {
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

}

?>