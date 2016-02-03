<?php
/**
 * Created by PhpStorm.
 * User: sujata.patne
 * Date: 23-12-2015
 * Time: 18:26
 */
include_once(APP."models/Page.php");

class PageController extends BaseController{
    private $page;

    public function __construct(){
        $this->page = new Page();
    }

    public function getAction($request){
        parent::display($request);
    }

    public function postAction($request){
        $data = $request->parameters;
        $result = $this->page->find(ICONDB, $data);
        echo json_encode($result);
    }

}
?>