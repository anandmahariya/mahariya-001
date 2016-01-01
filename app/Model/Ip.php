<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class Ip extends AppModel {
    
	var $name = 'Ip';
    var $primaryKey = '_id';
    var $useDbConfig = 'mongo';
    var $mongoSchema = array(
			'ip'=>array('type'=>'string'),
			'ip_long'=>array('type'=>'integer'),
			'as'=>array('type'=>'integer'),
			'registry'=>array('type'=>'string'),
			'as_name'=>array('type'=>'string'),
			'dns'=>array('type'=>'string'),
			'country_code'=>array('type'=>'string'),
			'country'=>array('type'=>'string'),
			'state'=>array('type'=>'string'),
			'city'=>array('type'=>'string'),
			'latitude'=>array('type'=>'string'),
			'longitude'=>array('type'=>'string'),
			'status'=>array('type'=>'string'),
			'created'=>array('type'=>'datetime'),
			'modified'=>array('type'=>'datetime'),
			);

    public function getIpLocation($ip){
        
        $response = (object) array('response'=>array('status'=>0,'result'=>null));
        
        //find data in database
        $resultSet = $this->find('first',array('conditions'=>array('ip'=>$ip)));
        if($resultSet){
        	$response->response['status'] = $resultSet['Ip']['status'];
            $response->response['result'] = $resultSet['Ip'];
        }else{
            $detail = new IpToLocation($ip);
            $data = array('ip'=>$ip,'ip_long'=> new MongoInt64(ip2long($ip)),'country_code'=>'','country'=>'','state'=>'','city'=>'','latitude'=>'','longitude'=>'','status'=>0);
            if(isset($detail->response['status']) && $detail->response['status'] == 1){
                $data = array_merge($data,$detail->response['result']);
                
                //check data is valid or not
                if(!empty($data['country_code']) && !empty($data['state']) && !empty($data['city'])){
                    $data['status'] = 1;
                }else{
                    $data['status'] = 0;
                }
            }else{
                $data['status'] = 0;
            }
            
            //data save in DB
            if($tmp = $this->save($data)){
                $response->response['status'] = $tmp['Ip']['status'];
                $response->response['result'] = $tmp['Ip'];
            }
        }
        return $response;
    }
}