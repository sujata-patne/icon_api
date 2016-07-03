<?php
require_once(APP."Models/Page.php");
require_once(APP."Models/Message.php");
require_once(APP."Models/Package.php");
include_once(APP."Controllers/config.class.php");
use Store\Config as Config;

class PackageController extends BaseController {

	public function __construct(){																	
		parent::__construct();
		$this->package = new Package();
	}
	
	public function createJsonObj( $request ){														  //create json object of request
		$json = json_encode( $request->parameters );   
      	return json_decode( $json );
	}
	
	public function createSuccessLogs( $request,$tunes,$controllerName ){						     //create succeess logs
		$response = array("status" => "SUCCESS", "status_code" => '200', 'Contents' => $tunes);
		$this->successLog->LogInfo('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
		$this->outputSuccess($response);
		return;
	}
	
	public function createErrorLogs( $jsonResMessage,$controllerName ){
		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
			$this->outputError($response);
			return;
		}
	}
	
	public function createEmptyLogs( $tunes,$emptyMsg,$controllerName,$request  ){					//in case of null output
		if(empty($tunes)){		//in case of no caller tunes
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => $emptyMsg );
			$this->successLog->LogInfo('TuneController:'.$controllerName."\r\n".'Request =>'.json_encode($request->parameters)."\r\n".'Response =>'.json_encode($response)."\r\n");
			$this->outputSuccess($response);
			return;
		}
	}
	
	public function createPreviewFileStreamingPath( $fileName ){												//create s3 streaming path
		
		$asset_path = '';
		$streamingURL = '';
		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$domain = 'http://d12m6hc8l1otei.cloudfront.net/';
		$expires = time() + (60*60);
		$this->config = new Config\Config();
	
		$files = explode(',',$fileName);
			
		for($i=0;$i<=count($files)-1;$i++){																//incomplete extra last file path  ---> -1
			$ext = pathinfo($files[$i], PATHINFO_EXTENSION);											//get file extension
			switch($ext)
			{
				case 'mp3':																				//path for audio files
					$asset_path[] = $domain.'preview_files/audio_files/'.$files[$i];
					break;
						
				case 'txt':
					$asset_path[] = $domain.'preview_files/text_files/'.$files[$i];											//path for text files
					break;		
					
				case '3gp':
				case 'mp4':
					$asset_path[]= $domain.'preview_files/video_files/'.$files[$i];
					break;
					
				case 'jpg':																			      //path for image files
				case 'jpeg':																			  //path for image files
				case 'gif':		
				case 'png':
					$asset_path[] = $domain.'preview_files/image_files/'.$files[$i];
					break;
				
			}
		}	
		
		for($i=0;$i<count($asset_path);$i++){
			if(!empty($asset_path)){
				$streamingURL[] = $this->config->create_signed_url($asset_path[$i], $private_key_filename, $key_pair_id, $expires);		
			}
		}
		
		return $streamingURL;
	}
	
	public function createSupportingFileStreamingPath( $fileName ){												//create s3 streaming path
		
		$asset_path = '';
		$streamingURL = '';
		$private_key_filename = '/var/www/api/v3/pk-APKAI6KQIZYCKQ2ZFREA.pem';
		$key_pair_id = 'APKAI6KQIZYCKQ2ZFREA';
		$domain = 'http://d12m6hc8l1otei.cloudfront.net/';
		$expires = time() + (60*60);
		$this->config = new Config\Config();
	
		$files = explode(',',$fileName);
			
		for($i=0;$i<=count($files)-1;$i++){																//incomplete extra last file path  ---> -1
			$ext = pathinfo($files[$i], PATHINFO_EXTENSION);											//get file extension
			switch($ext)
			{
				case 'mp3':																				//path for audio files
					$asset_path[] = $domain.'supporting_files/audio_files/'.$files[$i];
					break;
						
				case 'txt':
					$asset_path[] = $domain.'supporting_files/text_files/'.$files[$i];				   //path for text files
					break;		
					
				case '3gp':
				case 'mp4':
					$asset_path[]= $domain.'supporting_files/video_files/'.$files[$i];
					break;
					
				case 'jpg':																			  //path for image files
				case 'jpeg':																		 //path for image files
				case 'gif':		
				case 'png':
					$asset_path[] = $domain.'supporting_files/image_files/'.$files[$i];
					break;
				
			}
		}	
		
		for($i=0;$i<count($asset_path);$i++){
			if(!empty($asset_path)){
				$streamingURL[] = $this->config->create_signed_url($asset_path[$i], $private_key_filename, $key_pair_id, $expires);		
			}
		}
		
		return $streamingURL;
	}
	
	public function createThumbnailPath ( $thumbnailPath,$thumbnailSize,$metadataId ){				//create thumbnail path
		$CreatedThumbnailPath = '';	
		
		if($thumbnailSize != 'NA'){
			if($thumbnailPath != '' || $thumbnailPath != NULL){
				$sizes = explode(',',$thumbnailSize);
				$files = explode(',',$thumbnailPath);
				for($i=0;$i<=count($sizes)-1;$i++){
					if(!empty($sizes[$i])){
						$size = explode("*",$sizes[$i]);
					}
					$ext = pathinfo($files[$i], PATHINFO_EXTENSION);
					$thumbUrl = $metadataId.'_thumb_'.$size[0].'_'.$size[1].'.'.$ext;
					$CreatedThumbnailPath[] = 'http://d85mhbly9q6nd.cloudfront.net/'.$thumbUrl;
				}				
			}
		}	
			
			$CreatedThumbnailPath = $CreatedThumbnailPath != '' ? $CreatedThumbnailPath : 'NA';
			return $CreatedThumbnailPath;			
	}

    public function getPackageContents( $request ) {
		
		$emptyMsg 		= 'Package blocked/not published';
		$functionName   = 'getPackageContents';
		$jsonObj 		= self::createJsonObj( $request );
		$jsonResMessage = $this->package->validateJsonPackageId($request->parameters);
		self::createErrorLogs( $jsonResMessage,$functionName );
		
		$packageContents['Package Contents'] = $this->package->getDetails( 'getPackageContents',$jsonObj );
		$packageContents['valuePackPlan']	 = $this->package->getDetails('getValuePackPlanContents', $jsonObj );
		$packageContents['subscriptionPlan'] = $this->package->getDetails('getSubscriptionPlanContents', $jsonObj );
		$packageContents['alacartaPlan'] 	 = $this->package->getDetails('getAlacartaPlanContents', $jsonObj );
		$packageContents['offerPlan'] 		 = $this->package->getDetails('getOfferPlanContents', $jsonObj );
		
		$count = count($packageContents['Package Contents'])-1;
		
		for($i=0;$i<=$count-1;$i++){
			
			$metaDataId = $packageContents['Package Contents'][$i]['contentMetadataId'];
			$thumbNails = self::createThumbnailPath( $packageContents['Package Contents'][$i]['thumbnailFiles'],$packageContents['Package Contents'][$i]['thumbnailFileSize'],$metaDataId );
			$previewFiles = self::createPreviewFileStreamingPath( $packageContents['Package Contents'][$i]['previewFiles'] );
			$supportingFiles = self::createSupportingFileStreamingPath( $packageContents['Package Contents'][$i]['supportingFiles'] );
			
			$packageContents['Package Contents'][$i]['thumbnailFiles'] = $thumbNails;			//get all thumbnails
			$packageContents['Package Contents'][$i]['previewFiles']   = $previewFiles;			//get all preview fies
			$packageContents['Package Contents'][$i]['supportingFiles']   = $supportingFiles;	//get all preview fies
			unset($packageContents['Package Contents'][$i]['thumbnailFileSize']);				//remove thumbnail size array -- no need to display
			
		}	
		
		self::createEmptyLogs( $packageContents,$emptyMsg,$functionName,$request );
		self::createSuccessLogs( $request,$packageContents,$functionName );	
		
	}
	
	public function getPlans( $request ){
		
		$json = json_encode( $request->parameters );
		$package = new Package();
		$jsonObj = json_decode($json);

		$jsonResMessage = $package->validateJsonPackageId($request->parameters);

		if(!empty($jsonResMessage) || strlen($jsonResMessage) != 0 || $jsonResMessage != ''){		//in case of missing parameters
			$response = array("status" => "ERROR", "status_code" => '400', 'msgs' => $jsonResMessage);
			$this->errorLog->LogError('PackageController:getPlans#'.json_encode($response));
			$this->outputError($response);
			return;
		}

		$packagePlans['ValuePackPlans'] = $package->getDetails('getValuePackPlans', $jsonObj );
		$packagePlans['SubscriptionPlans'] = $package->getDetails('getSubscriptionPlans', $jsonObj );
		$packagePlans['AlacartPlans'] = $package->getDetails('getAlacartaPlans', $jsonObj );
		$packagePlans['OfferPlans'] = $package->getDetails('getOfferPlans', $jsonObj );

		
		if(empty($packagePlans['ValuePackPlans']) && empty($packagePlans['SubscriptionPlans']) || empty($packagePlans['AlacartPlans']) && empty($packagePlans['OfferPlans'])){		//in case of non-existing package
			$response = array("status" => "SUCCESS", "status_code" => '200', 'msgs' => 'No Plans available');
			$this->successLog->LogInfo('PackageController:getPlans#'.json_encode($response));
			$this->outputSuccess($response);
			return;
		}
			
			$response = array("status" => "SUCCESS", "status_code" => '200', 'Plans' => $packagePlans);
			$this->successLog->LogInfo('PackageController:getPlans#'.json_encode($response));
			$this->outputSuccess($response);
			return;	


	}

}
?>