<?php

use VOP\Utils\PdoUtils;

require_once(APP."Daos/DeviceDao.php");
require_once(APP."Models/BaseModel.php");
require_once(APP."Models/Message.php");
require_once(APP."Utils/PdoUtils.php");

class DeviceInfo extends BaseModel {

	public $dc_id;
	public $dc_device_id;
	public $dc_make;
	public $dc_model;
	public $dc_architecture;
	public $dc_RAM;
	public $dc_internal_memory;
	public $dc_ROM;
	public $dc_GPU;
	public $dc_CPU;
	public $dc_chipset;
	public $dc_OS;
	public $dc_OS_version;
	public $dc_pointing_method;
	public $dc_width;
	public $dc_height;

	public function __construct($json = NULL) {
		if (is_null($json)) {
			return;
		}

		$this->setValuesFromJsonObj($json);
	}
	
	public function validateJson($jsonObj) {
		$requiredProps = array('dc_device_id');
	
		$message = $this->hasRequiredProperties($jsonObj, $requiredProps);
		return $message;
	}

	public function setValuesFromJsonObj($jsonObj) {
		$result = $this->setValuesFromJsonObjParent($jsonObj);

		if (!$result) {
			return $result;
		}

		return true;
	}

	public  function generateDeviceId() {

		$udid = UuidUtils::uuid();
		$newUuid = str_replace('-', '', $udid);

		return $newUuid;
	}
	
	public function getDeviceDetailsByDeviceId( $deviceId ){
		$dbConnection = PdoUtils::obtainConnection('SITE_USER');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
	
		$dbConnection->beginTransaction();
		
		$device = null;
		
		try {
			$deviceDao = new DeviceDao($dbConnection);
			$device = $deviceDao->getDeviceDetailsByDeviceId( $deviceId );
		
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $device;
	}
	
	public function getTemplateDetailsByDeviceHeightAndWidth($deviceHeight, $deviceWidth ) {
		$dbConnection = PdoUtils::obtainConnection('CMS');
		
		if ($dbConnection == null) {
			return Message::ERROR_NO_DB_CONNECTION;
		}
		
		$dbConnection->beginTransaction();
		
		$device = null;
		
		try {
			$deviceDao = new DeviceDao($dbConnection);
			$device = $deviceDao->getTemplateDetailsByDeviceHeightAndWidth( $deviceHeight, $deviceWidth );
		
			$dbConnection->commit();
		} catch (\Exception $e) {
			$dbConnection->rollBack();
			print_r( $e );exit;
		}
		
		PdoUtils::closeConnection($dbConnection);
		return $device;
	}
}
?>