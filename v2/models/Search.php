<?php
class Search{
	private $storeId;
	private $keyword;
			
	public function find($dbName, $data = array()){			
		$this->storeId = $data['storeId'];
		$this->keyword = $data['keyword'];
		$result = array();
		
		$db = new \mysqli(DBHOST, DBUSER, DBPASSWD, $dbName);
		
		if($db->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		
		$vendorIds = $this->getVendorIds($db);
		
		if(empty($vendorIds)){
			print_r("No Vendor found for this store!");
			exit();
		}
		
		$vendor_id = implode(",",$vendorIds); 
		
		$query = "select cm_id,cm_title,cm_genre,propertyname,vd_name,cm_thumb_url,fileUrl,cm_vendor,cm_property_id,parentid,parentname,content_type_id,content_type,group_concat( delivery_type ) as delivery_type from(  select cm_key_words,cm_id,cm_title,cm_vendor,cm_content_type,cm_property_id,cm_genre from content_metadata where cm_state = 4 and cm_property_id is not null and cm_starts_from <= NOW() and cm_expires_on >= NOW() and cm_vendor in (".$vendor_id.") order by cm_modified_on desc )cm inner join(SELECT cm_id as propertyid ,cm_title as propertyname FROM content_metadata where cm_vendor in (".$vendor_id.") and  cm_property_id is null and cm_starts_from <= NOW() and cm_expires_on >= NOW() and cm_is_active =1 )prop on(cm.cm_property_id =prop.propertyid)inner join(SELECT vd_id,vd_name FROM icn_vendor_detail where vd_id in (".$vendor_id.") and  vd_is_active  =1 and vd_starts_on <= NOW() and vd_end_on >= NOW())vd on(cm.cm_vendor =vd.vd_id) inner join (SELECT mct_parent_cnt_type_id,mct_cnt_type_id,mct_delivery_type_id FROM icn_manage_content_type)cnt on (cnt.mct_cnt_type_id = cm.cm_content_type) inner join (select cd_id as parentid,cd_name as parentname from catalogue_detail where cd_name = 'Video')parent on(parent.parentid  = cnt.mct_parent_cnt_type_id)inner join (select cd_id as content_type_id ,cd_name as content_type  from catalogue_detail)subcnt on(subcnt.content_type_id  = cnt.mct_cnt_type_id) inner join (select * from multiselect_metadata_detail ) mmd on (cnt.mct_delivery_type_id=mmd.cmd_group_id) inner join (select cmd_group_id,cmd_entity_type,cmd_entity_detail from multiselect_metadata_detail )keymmd on(cm.cm_key_words = keymmd.cmd_group_id and keymmd.cmd_entity_type = cm.cm_content_type) inner join (select cd_id as keyword_id ,cd_name as keyword from catalogue_detail as a ,catalogue_master as b where a.cd_name like '%".$this->keyword."%' and b.cm_name = 'Search Keywords' and a.cd_cm_id = b.cm_id )keyw on(keyw.keyword_id =keymmd.cmd_entity_detail)
		inner join(select cd_name as delivery_type,cd_id as delivery_type_id from catalogue_detail )del on (del.delivery_type_id =mmd.cmd_entity_detail) inner join (SELECT group_concat(cf_url) as fileUrl ,cf_cm_id FROM content_files group by cf_cm_id )cm_files on(cm.cm_id = cm_files.cf_cm_id )left  join (SELECT group_concat(cft_thumbnail_img_browse) as cm_thumb_url ,cft_cm_id FROM content_files_thumbnail group by cft_cm_id )cth on(cm.cm_id = cth.cft_cm_id ) group by cm_id,cm_title,propertyname,vd_name,cm_thumb_url,fileUrl,cm_vendor,cm_property_id,parentid,parentname,content_type_id,content_type";
		
		$searchResult = mysqli_query($db, $query);
		
		if( mysqli_num_rows($searchResult) > 0 ){
			while($row = mysqli_fetch_assoc($searchResult)){
				$result[] = $row;
			}	
		}
		
		return $result;
	}
	
	private function getVendorIds($con){
		$vendors = array();
		
		$query = "select vd_id from icn_store as Store, multiselect_metadata_detail as Grouping, icn_vendor_detail as Vendor where Store.st_vendor = Grouping.cmd_group_id and Grouping.cmd_entity_detail = Vendor.vd_id and Store.st_id = ".$this->storeId;
		
		$result = mysqli_query($con, $query);
		
		if( mysqli_num_rows($result) > 0 ){
			while($row = mysqli_fetch_assoc($result)){
				$vendors[] = $row['vd_id'];
			}			
		}
		mysqli_free_result($result);
		return $vendors;
	}
	
}
?>