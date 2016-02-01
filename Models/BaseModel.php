<?php

class BaseModel {

    public $created_on;
    public $updated_on;
    public $created_by;
    public $updated_by;

    protected function hasRequiredProperties($obj, $propertiesArr) {


        if (empty($obj)) {
            return 'Invalid JSON';
        }

        foreach ($propertiesArr as $property) {
            if (!property_exists($obj, $property)) {
                return 'Invalid JSON - Property Missing: ' . $property;
            }
        }
		 
        return Message::SUCCESS;
    }

    protected function requiredPropertiesNotNullOrEmpty($propertiesArr) {

        foreach ($propertiesArr as $property) {

            if (!property_exists($this, $property)) {
                return 'Property Missing: ' . $property;
            }

            if (empty($this->$property)) {
                return 'Property Missing: ' . $property;
            }
        }

        return Message::SUCCESS;
    }

    public function setCCUUToNow($userId) {
        $now = time();

        $this->created_on = $now;
        $this->updated_on = $now;

        $this->created_by = $userId;
        $this->updated_by = $userId;
    }

    public function unsetValues($propertiesArr) {
 
        foreach ($propertiesArr as $property) {
            if (property_exists($this, $property)) {
                unset($this->$property);
            }
        }
    }

    public function setValuesFromJsonObjParent($jsonObj) {

        if (is_null($jsonObj)) {
            return false;
        }

        foreach ($jsonObj as $property => $val) {
        	
            $type = gettype($val);

            if (!$this->isBasicType($type)) {
                continue;
            }

            if (property_exists($this, $property)) {
                $this->$property = $val;
            }
        }

        return true;
    }

    public function isBasicType($type) {

        switch ($type) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                return true;
            default :
                return false;
        }
    }

}
