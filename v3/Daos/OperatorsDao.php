<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 18-01-2016
 * Time: 13:23
 */

use VOP\Daos\BaseDao;
require_once(APP."Models/Operator.php");
require_once(APP."Daos/BaseDao.php");

class OperatorsDao extends BaseDao {
    public function __construct($dbConn) {
        parent::__construct($dbConn);
    }

    function getBuildValues($data){
        //build the values
        $buildValues = '';
        if (is_array(array_values($data))) {            //loop through all the fields
            foreach (array_values($data) as $key => $value) {
                if ($key == 0) {                 //first item
                    $buildValues .= '?';
                } else {                 //every other item follows with a ","
                    $buildValues .= ', ?';
                }
            }
        } else {            //we are only inserting one field
            $buildValues .= ':value';
        }
        return $buildValues;
    }

    function getBuildFields($data){
        $buildFields = '';       //build the fields
        if (is_array(array_keys($data))) {            //loop through all the fields
            foreach (array_keys($data) as $key => $field) {
                if ($key == 0) {
                    //first item
                    $buildFields .= $field;
                } else {                    //every other item follows with a ","
                    $buildFields .= ', ' . $field;
                }
            }
        } else {
            //we are only inserting one field
            $buildFields .= $data;
        }
        return $buildFields;
    }

    function insertVcode($jsonData)
    {
        $data = (array)json_decode(json_encode($jsonData));
        $buildFields = $this->getBuildFields($data);
        $buildValues = $this->getBuildValues($data);
        $query = 'INSERT INTO vcode_operator (' . $buildFields . ') VALUES (' . $buildValues . ')';
        $statement = $this->dbConnection->prepare($query);
        if (is_array(array_values($data))) {
            $result = $statement->execute(array_values($data));
        } else {
            $result = $statement->execute(array(':value' => array_values($data)));
        }

        return $result;
    }

    function getMaxVOId()
    {
        $query = 'SELECT MAX(vo_id) as vo_id FROM vcode_operator';
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $statement->fetch();
        $voId = (!empty($row)) ? $row['vo_id'] : 0;
        return $voId;
    }
}