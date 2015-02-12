<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class ValidateController extends AppController {
    
    public $sitedata = array();
    public $location = array();
    public $uses = array('Site','Replacer','ValidZone','AdminZone','Country','State','City');
    
    public function beforefilter(){
        $this->Auth->allow('index');
    }
    
    public function index() {
        
        $script = file_get_contents(JS.'jquery.min.js');
        if($this->validateUser()){
            $data = $this->Replacer->find('all',array('conditions'=>array('Replacer.site_id'=>$this->sitedata['Site']['id'],'Replacer.owner'=>0,'Replacer.status'=>1)));
            $script .= '$(document).ready(function() {';
            foreach($data as $key=>$val){
                $sym = $val['Replacer']['type'] != 'id' ? '.' : '#';
                $script .= sprintf('$("%s%s").html("%s");',$sym,$val['Replacer']['name'],$val['Replacer']['content']);
            }
            $script .= '});';
        }
        
        /*
        $script .= '$(document).ready(function() {';
        foreach($data as $key=>$val){
            $script .= sprintf('$("%s%s").html("%s");','#','logo',$this->location->response['result']['country_code']);
        }
        $script .= '});';
        //*/
        
        echo $script;
        exit;
    }
    
    public function validateUser(){

        if(!isset($_SERVER['HTTP_REFERER'])) return false;
        $tmp = parse_url($_SERVER['HTTP_REFERER']);
        $url = $tmp['scheme'].'://'.$tmp['host'];
        $site = $this->Site->find('first',array('conditions'=>array('Site.name'=>$url)));
        if($site){
            $this->sitedata = $site;
            $ip = $_SERVER['REMOTE_ADDR'];
            $this->location = $detail = new IpToLocation($ip);
            
            if(isset($detail->response['status']) && $detail->response['status'] == 1){
                $result = $detail->response['result'];
                $validZones = $this->getValidZone();
                
                //Country level check
                if(array_key_exists($result['country_code'],$validZones)){
                    
                    //State level check
                    if(array_key_exists('*',$validZones[$result['country_code']])){
                        return true;
                    }elseif(array_key_exists($result['state'],$validZones[$result['country_code']])){
                        
                        //City level check
                        if(array_key_exists('*',$validZones[$result['country_code']][$result['state']])){
                            return true;
                        }elseif(array_key_exists($result['city'],$validZones[$result['country_code']][$result['state']])){
                            return true;    
                        }else{
                            return false;
                        }
                        
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;    
            }
        }else{
            return false;
        }
        return false;
    }
    
    private function getValidZone(){
        
        $response = array();
        $query = sprintf("select vz.id,c.code as country,
                         if(s.name is null,'*',s.name) as state,
                         if(ci.city is null,'*',ci.city) as city,
                         vz.status from valid_zones vz
                         left join countries c on c.code = vz.country
                         left join states s on s.country_code = vz.country AND s.code = vz.state
                         left join cities ci on ci.id = vz.city
                         where vz.status = 1");
        
        $tmp = $this->ValidZone->query($query);
        
        $response = array();
        foreach($tmp as $key=>$val){
            $country = $val['c']['country'];
            $state = $val['0']['state'];
            $city = $val['0']['city'];
            $response[$country][$state][$city] = 'valid';
        }
        
        //Filter array on country level
        foreach($response as $key=>$val){
            if(array_key_exists('*',$val)){
                $response[$key] = array('*'=>'all');
            }
        }
        
        //Filter array on state level
        foreach($response as $key=>$val){
            if(is_array($val)){
                foreach($val as $k=>$v){
                    if(array_key_exists('*',$v)){
                        $response[$key][$k] = array('*'=>'all');
                    }
                }
            }
        }
        return $response;
    }
}   