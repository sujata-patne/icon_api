<?php

use VOP\Daos\BaseDao;
require_once(APP."Models/DeviceInfo.php");
require_once(APP."Daos/BaseDao.php");

class DeviceDao extends BaseDao {

	public function __construct($dbConn) {
		parent::__construct($dbConn);
	}

	private function deviceFromRow($row) {
		$device = new DeviceInfo();
		$device->dc_id 			 	= $row['dc_id'];
		$device->dc_device_id    	= $row['dc_device_id'];
		$device->dc_make 		 	= $row['dc_make'];
		$device->dc_model 		 	= $row['dc_model'];
		$device->dc_architecture 	= $row['dc_architecture'];
		$device->dc_RAM 		 	= $row['dc_RAM'];
		$device->dc_internal_memory = $row['dc_internal_memory'];
		$device->dc_ROM 			= $row['dc_ROM'];
		$device->dc_GPU 			= $row['dc_GPU'];
		$device->dc_CPU 			= $row['dc_CPU'];
		$device->dc_chipset 		= $row['dc_chipset'];
		$device->dc_OS 				= $row['dc_OS'];
		$device->dc_OS_version 		= $row['dc_OS_version'];
		$device->dc_pointing_method = $row['dc_pointing_method'];
		$device->dc_width 			= $row['dc_width'];
		$device->dc_height 			= $row['dc_height'];
	
		return $device;
	}
	
	public function getDeviceDetailsByDeviceId( $deviceId ) {
	 
		$query = "SELECT * FROM device_compatibility WHERE dc_device_id = :deviceId";
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam(':deviceId', $deviceId);
	 
		$statement->execute();
		
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		
		$device = null;
		
		if (($row = $statement->fetch()) != FALSE) {
			$device = $this->deviceFromRow($row);
		}
		
		return $device;
	}
	
	public function getTemplateDetailsByDeviceHeightAndWidth( $deviceHeight, $deviceWidth ) {
	
		$query = "SELECT 
					  ct.ct_group_id, 
					  ct.ct_param_value as height, 
					  ct.ct_param as height_value,  
					  ct1.ct_param_value as width, 
					  ct1.ct_param  as width_value
				   FROM 
					  content_template as ct 
				   JOIN 
					  content_template as ct1 ON (ct1.ct_group_id = ct.ct_group_id AND ct.ct_param_value = 'height' AND ct1.ct_param_value = 'width')
				   WHERE ct.ct_param = :deviceHeight and ct1.ct_param = :deviceWidth";
		
		$statement = $this->dbConnection->prepare($query);
		$statement->bindParam( ':deviceHeight', $deviceHeight );
		$statement->bindParam( ':deviceWidth', $deviceWidth );		 
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
	
		$device = null;
	
		if (($row = $statement->fetch()) != FALSE) {
			$device = $row;
		}
	
		return $device;
	}
}
?>