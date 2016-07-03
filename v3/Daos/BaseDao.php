<?php

namespace VOP\Daos;

class BaseDao {

    public $dbConnection;

    public function __construct($dbConn) {
        $this->dbConnection = $dbConn;
    }
    public function logCurlAPI($extractParams){
        $logFile = LOGS.'CurlDaos_'.date('d-m-Y-H').'.log';
        $fs = fopen($logFile, 'a') or die("can't open file");
        fwrite($fs, "\r\n");
        file_put_contents($logFile, var_export($extractParams, true),FILE_APPEND);

    }
    protected function ccuuFromRow($obj, $row) {
        $obj->created_at = $row["created_on"];
        $obj->created_by = $row["created_by"];
        $obj->updated_at = $row["updated_on"];
        $obj->updated_by = $row["updated_by"];
    }

    public function bindCCUU(&$statement, $model) {
        $now = time();
        $statement->bindParam(':created_on', $now);
        $statement->bindParam(':created_by', $model->created_by);
        $statement->bindParam(':updated_on', $now);
        $statement->bindParam(':updated_by', $model->updated_by);
    }

}
