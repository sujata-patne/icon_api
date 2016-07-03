<?php

require_once(APP."Models/ContentDownload.php");
require_once(APP."Models/Message.php");
require_once(APP."Controllers/DeviceController.php");
include_once(APP."Controllers/config.class.php");

use Store\Config as Config;

class ContentDownloadController extends BaseController
{
	
	private $UserAgent;
	private $wurflConfigCloud;
	private $wurflConfigStandalone;
	private $serviceHostInternal;
	
    public function __construct(){
        parent::__construct();
		
    }
    public function getAction( $request ){
        parent::display($request);
    }
    public function postAction( $request ){
        echo "coming";
        exit;
    }
    public function getDownloadsWithUserAuth( $request ){

        $json = json_encode( $request->parameters );
        $contentDownload = new ContentDownload();
        $jsonObj = json_decode($json);

        $jsonResMessage = $contentDownload->validateJsonMSISDNEligibility($jsonObj);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentDownloadController:getDownloadsWithUserAuth#'.json_encode($response));
			$this->outputError($response);
			return;
		}

        $ContentDownloads = $contentDownload->getDownloads('getDownloadsWithUserAuth', $jsonObj );

		if(empty($ContentDownloads)){		//in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'Invalid MSISDN or Eligibility');
			$this->successLog->LogInfo('ContentDownloadController:getDownloadsWithUserAuth#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'ContentDownloads' => $ContentDownloads);
			$this->successLog->LogInfo('ContentDownloadController:getDownloadsWithUserAuth#'.json_encode($response));
			$this->outputSuccess($response);
			return;

    }
    public function getPlanHistory( $request ){

        $json = json_encode( $request->parameters );
        $contentDownload = new ContentDownload();
        $jsonObj = json_decode($json);

        $jsonResMessage = $contentDownload->validateJsonMSISDN($jsonObj);

        if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentDownloadController:getPlanHistory#'.json_encode($response));
			$this->outputError($response);
			return;
		}

        $ContentDownloadsHistory = $contentDownload->getDownloads('getPlanHistory', $jsonObj );

		if(empty($ContentDownloadsHistory) || $ContentDownloadsHistory == null || !isset($ContentDownloadsHistory)){
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'No Plan History found');
			$this->successLog->LogInfo('ContentDownloadController:getPlanHistory#'.json_encode($response));
            $this->output($response);
            return;
		}else{
			$response = array("status" => "SUCCESS", "status_code" => '200', 'PlanHistory' => $ContentDownloadsHistory);
			$this->successLog->LogInfo('ContentDownloadController:getPlanHistory#'.json_encode($response));
			$this->outputSuccess($response);
			return;	 
		}

    }
    public function getUserDownloadHistory( $request ){

        $json = json_encode( $request->parameters );
        $contentDownload = new ContentDownload();
        $jsonObj = json_decode($json);

        $jsonResMessage = $contentDownload->validateJsonMSISDNAppId($jsonObj);

        if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentDownloadController:getUserDownloadHistory#'.json_encode($response));
			$this->outputError($response);
			return;
		}

        $ContentDownloads = $contentDownload->getDownloads('getUserDownloadHistoryByMSISDNByAppID', $jsonObj );
		
		if(empty($ContentDownloads) || $ContentDownloads == null || !isset($ContentDownloads)){

			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'No Download History found');
			$this->successLog->LogInfo('ContentDownloadController:getUserDownloadHistory#'.json_encode($response));
            $this->output($response);
            return;
		}else{
			$response = array("status" => "SUCCESS", "status_code" => '200', 'UserDownloadHistory' => $ContentDownloads);
			$this->successLog->LogInfo('ContentDownloadController:getUserDownloadHistory#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
		
    }
	
	
	public function contentDownloadData( $request ){
		
		$URLs = array();
		$signed_url = '';
		$smil_url = '';
		
		$required = array();
		$required['metadata_id'] = $request->parameters['metadata_id'];
		$required['content_id'] = $request->parameters['content_id'];
		$required['child_id'] = $request->parameters['child_id'];
		$required['vendor_id'] = $request->parameters['vendor_id'];
		$this->config = new Config\Config();
		//$device = new DeviceController();
		
		//$mobileInfo = $device->mobileInfo;
		
		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$domain = 'http://d12m6hc8l1otei.cloudfront.net/';
		$expires = time() + (5*60);
		
		$json = json_encode( $request->parameters );
        $contentDownload = new ContentDownload();
        $jsonObj = json_decode($json);
		
        $jsonResMessage = $contentDownload->validateJson($jsonObj);

        if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentDownloadController:contentDownloadData#'.json_encode($response));
			$this->outputError($response);
			return;
		}
		
		$contents = $contentDownload->getContentDownloadPath( $required );	
		
		if(!empty($contents)){
			if($contents[0]['contentType'] == 'Video'){
				if ($cont_reso_type == 176){
					$asset_path  = $videos[0]['cm_downloading_url'].'.3gp';
				}elseif ($cont_reso_type == 240){
					$asset_path  = $videos[0]['cm_downloading_url'].'_240p.mp4';
				}elseif ($cont_reso_type == 360){
					$asset_path  = $videos[0]['cm_downloading_url'].'_360p.mp4';	
				}else{
					$asset_path  = $videos[0]['cm_downloading_url'].'_720p.mp4';
				}
			}else if($contents[0]['contentType'] == 'Audio'){
				
				$asset_path = $domain.'audio/'.$contents[0]['TuneName'];
				$signed_url = $this->config->create_signed_url($asset_path, $private_key_filename, $key_pair_id, $expires);
				$URLs['signed_url'] = $signed_url;	
		
			}else{
				if( isset($mobileInfo['Wallpaper_Width']) and !empty($mobileInfo['Wallpaper_Width'])){
					$WallpaperWidth = $mobileInfo['Wallpaper_Width'];
					$WallpaperHeight = $mobileInfo['Wallpaper_Height'];
				}else{
					if( $mobileInfo['Resolution_Width'] > 800 ){
						$WallpaperWidth = '720';
						$WallpaperHeight = '1280';
					}else{
						if($mobileInfo['Resolution_Width'] == 800){
							if($mobileInfo['Resolution_Width'] == 800 and $mobileInfo['Resolution_Height'] == 1280){
								$WallpaperWidth = '720';
								$WallpaperHeight = '1280';
							}else{
								$WallpaperWidth = '800';
								$WallpaperHeight = '600';
							}
						}elseif($mobileInfo['Resolution_Width'] < 800 and $mobileInfo['Resolution_Width'] >= 768){
							$WallpaperWidth = '720';
							$WallpaperHeight = '1280';
						}else{
							$WallpaperWidth = $mobileInfo['Resolution_Width'];
							$WallpaperHeight = $mobileInfo['Resolution_Height'];
						}
					}
				}
				
				$alldmUrlParams = explode('/', $contents[0]['DownloadingURL']);
			
				$asset_path = $domain.'wallpapers/'.$jsonObj->cd_cd_id.'_'.$WallpaperWidth.'_'.$WallpaperHeight.'.'.$alldmUrlParams[5];
			
				$signed_url = $this->config->create_signed_url($asset_path, $private_key_filename, $key_pair_id, $expires);
			
				$URLs['signed_url'] = $signed_url;
				
			}
			
		}else{
			
			$smil_url = $contentDownload->getDownloads( 'getSmilURL', $required );
			$URLs['smil_url']   = $smil_url;
		}
		
		$contentDownloaded = array();
		$contentDownloaded['msisdn'] 			= $jsonObj->msisdn;
		$contentDownloaded['metadata_id'] 		= $jsonObj->metadata_id;
		$contentDownloaded['cd_download_count'] = 1;
		$contentDownloaded['content_id'] 		= $jsonObj->content_id;
		$contentDownloaded['app_id'] 			= $jsonObj->app_id;
		$contentDownloaded['promo_id'] 			= $jsonObj->promo_id;
		$contentDownloaded['package_id'] 		= $jsonObj->package_id == NULL ? '0': $jsonObj->package_id;
		$contentDownloaded['pack_id'] 			= $jsonObj->pack_id == NULL ? '0' : $jsonObj->pack_id;
		$contentDownloaded['plan_id'] 			= $jsonObj->plan_id == NULL ? '0' : $jsonObj->plan_id;
		$contentDownloaded['child_id'] 			= $jsonObj->child_id;
		$contentDownloaded['cd_download_date'] 	= date('Y-m-d H:i:s');
		//$contentDownloaded['cd_response_url'] 	= $signed_url;   //--- increase length in db for audio downloading URL
		
		$contentDownloadedjson = json_encode( $contentDownloaded );
		$contentDownloadedjsonObj = json_decode($contentDownloadedjson);
		
		$contentDownload->getDownloads('setContentDownload', $contentDownloadedjsonObj );
		
		if(empty($URLs)){		//in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'Download/Streaming URLs not available');
			$this->outputSuccess($response);
			$this->successLog->LogInfo('ContentDownloadController:contentDownloadData#'.json_encode($response));
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'URls' => $URLs);
			$this->outputSuccess($response);
			$this->successLog->LogInfo('ContentDownloadController:contentDownloadData#'.json_encode($response));
			return;	
		
		
	}
	
	public function contentDownloadAll( $request ){
			
		$URLs = array();
		$signed_url = '';
		$smil_url = '';
		
		$required = array();
		$required['metadata_id'] = $request->parameters['metadata_id'];
		$required['child_id'] = '';
		$this->config = new Config\Config();
		
		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$domain = 'http://d12m6hc8l1otei.cloudfront.net/';
		$expires = time() + (60*60);
		
		$json = json_encode( $request->parameters );
        $contentDownload = new ContentDownload();
        $jsonObj = json_decode($json);
		
        $jsonResMessage = $contentDownload->validateJsonForAll($jsonObj);

        if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('ContentDownloadController:contentDownloadData#'.json_encode($response));
			$this->outputError($response);
			return;
		}
		
		$contents = $contentDownload->getContentDownloadPathForAll( $required );
		
		for($i=0;$i<count($contents);$i++){
			
			$ext = pathinfo($contents[$i]['FileName'], PATHINFO_EXTENSION);
			
			switch($ext)
			{
				case 'mp3':																				//path for audio files
					$asset_path[] = $domain.'audio/'.$contents[$i]['FileName'];
					break;
						
				case 'txt':
					$asset_path[] = $domain.'text/'.$contents[$i]['FileName'];				   //path for text files
					break;		
					
				case '3gp':
				case 'mp4':
					$asset_path[]= $domain.'video/'.$contents[$i]['FileName'];
					break;
					
				case 'jpg':																			  //path for image files
				case 'jpeg':																		 //path for image files
				case 'gif':		
				case 'png':
					$asset_path[] = $domain.'wallpapers/'.$contents[$i]['FileName'];
					break;
					
				case 'apk':																				//path for audio files
				case 'jar':																				//path for audio files
				case 'jad':																				//path for audio files
					$asset_path[] = $domain.'games/'.$contents[$i]['FileName'];
					break;	
				
			}
			
		}
		
		for($i=0;$i<count($asset_path);$i++){
			if(!empty($asset_path)){
				$downloadingURL[] = $this->config->create_signed_url($asset_path[$i], $private_key_filename, $key_pair_id, $expires);		
			}
		}
		
		if(empty($downloadingURL)){		//in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'Download URLs not available');
			$this->outputSuccess($response);
			$this->successLog->LogInfo('ContentDownloadController:contentDownloadData#'.json_encode($response));
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'Downloading URLs' => $downloadingURL);
			$this->outputSuccess($response);
			$this->successLog->LogInfo('ContentDownloadController:contentDownloadData#'.json_encode($response));
			return;	
		
		
	}
}

?>