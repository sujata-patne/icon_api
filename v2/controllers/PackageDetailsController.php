<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 23-12-2015
 * Time: 18:37
 */

include_once(APP."models/PackageDetails.php");

class PackageDetailsController extends BaseController{
    private $packageDetails;

    public function __construct(){
        $this->packageDetails = new PackageDetails();
    }

    public function getAction($request){
        parent::display($request);
    }

    public function postAction($request){
        parent::display($request);
        exit();
        $data = $request->parameters;
        $result = $this->packageDetails->find(ICONDB, $data);
        echo json_encode($result);
    }

}
?>