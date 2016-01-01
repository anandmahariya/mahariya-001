<?php
class ApiController extends AppController {

	public $uses = array('Domain','Request','Shortern','Ip');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->Auth->allow();
    }

	/***
	All the shortern api services start with s 
	some examples are below mentain
	check shortern : s
	create shortern : cs
	check shortern alias : csa
	*/
	public function s($key=null) {
		$response = array();
        $params = array('fields' => array('_id'=>0),'conditions' => array('key' => $key));
        $resultSet = $this->Shortern->find('first',$params);
        if($resultSet){

            $response = array('url'=>$resultSet['Shortern']['url'],
         						'status'=>$resultSet['Shortern']['status'],
         						'redirect'=>$resultSet['Shortern']['redirect'],
         						'user'=>array('valid'=>1,
         										'popup_url'=>'http://www.jabong.com'));
        }else{
        	$response = array('response'=>array('error'=>1,
        					  'disp_msg'=>'Key not match',
        					  'sys_msg'=>'Key not match'));
        }          

        echo json_encode($response);
        exit; 
	}
}