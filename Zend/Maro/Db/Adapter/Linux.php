<?php
class Maro_Db_Adapter_Linux implements Maro_Db_Interface
{	
    private $_db;
	function __construct()
        {   
            try {
                $str = Globals::getconfig("fastsync");
                $data = explode(";", $str->db->conn);
                $servername = $data[0];
                $username = $data[1];
                $password = $data[2];
                $myDB = "FAST_HNAM_DBTG"; 
                //connection to the database
//                $dbhandle = mssql_connect($servername, $username, $password);
                $this->_db = mssql_connect($servername, $username, $password)
                    or die("Couldn't connect to SQL Server on $servername"); 
                  //select a database to work with
                  $selected = mssql_select_db($myDB, $this->_db)
                    or die("Couldn't open database $myDB"); 
                return $this->_db;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        public function delete($data, $where) {

        }

        public function excute($sql) {
            mssql_select_db('FAST_HNAM_DBTG', $this->_db);
            $result = mssql_query($sql);
            return $result;
        }

        public function insert($table) {

        }

        public function update($table, $data, $where) {

        }

    public function select($sql) {
        mssql_select_db('FAST_HNAM_DBTG', $this->_db);
        $result = mssql_query($sql);
        $rs = mssql_fetch_array($result);
        return $rs;
    }
    public function select2($sql) {
        mssql_select_db('FAST_HNAM_DBTG', $this->_db);
        $result = mssql_query($sql);
        while($row = mssql_fetch_array($result))
        {
            $rs[] = $row;
        }
        return $rs;
    }

}
?>