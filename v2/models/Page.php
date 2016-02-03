<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 23-12-2015
 * Time: 18:28
 */
class Page {
    private $pageId;

    public function find($dbName, $data = array()){
        $this->pageId = $data['pageId'];
        $result = array();

        $db = new \mysqli(DBHOST, DBUSER, DBPASSWD, $dbName);

        if($db->connect_errno > 0){
            die('Unable to connect to database [' . $db->connect_error . ']');
        }

        echo $query = "SELECT
						pub.pp_page_file as pageName,
						pub.pp_sp_st_id as storeId,
						pub_map.pmpp_sp_pkg_id as packageId ,
						portlet.ppp_id as portletId,
						pub_map.pmpp_id as portletMapId,
						SHA1(pkg.sp_modified_on) as token
					FROM
						icn_pub_map_portlet_pkg  AS pub_map
				    Right OUTER JOIN
						icn_pub_page_portlet AS portlet ON ( pub_map.pmpp_ppp_id = portlet.ppp_id )
				    Right OUTER JOIN
						icn_pub_page AS pub ON ( pub.pp_id = portlet.ppp_pp_id )
					JOIN icn_store_package AS pkg ON pkg.sp_pkg_id = pub_map.pmpp_sp_pkg_id
				   	WHERE
						pub.pp_id = ".$this->pageId."
				   		AND portlet.ppp_is_active = 1
						AND ISNULL( portlet.ppp_crud_isactive )
				   		AND ISNULL( pub.pp_crud_isactive )
						AND ISNULL( pub_map.pmpp_crud_isactive )
						AND portlet.ppp_pkg_allow >= 0
					ORDER BY
						portlet.ppp_id,pub_map.pmpp_id ";
        exit;
        $pageResult = mysqli_query($db, $query);

        if( mysqli_num_rows($pageResult) > 0 ){
            while($row = mysqli_fetch_assoc($pageResult)){
                $result[] = $row;
            }
        }

        return $result;
    }
}