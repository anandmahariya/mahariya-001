<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class GetscriptController extends AppController {
    
    public $sitedata = array();
    public $location = array();
    public $condition = array();
    public $proxy_comment = '';
    public $valid_comment = '';
    public $uses = array('Site','Replacer','ValidZone','AdminZone','Country','State','City','Ip','Request','Option','Blockip');
    
    public function beforefilter(){
        $this->Auth->allow('index');
        
        //Condition load 
        $tmp = $this->Option->find('first',array('conditions'=>array('key'=>'conditions')));
        if(isset($tmp) && $tmp['Option']['value']!=''){
            $this->condition = unserialize($tmp['Option']['value']);
        }
    }
    
    public function index() {
        
        if(isset($_POST['h'])){
            
            $script = '<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>';
            $header = (Array) json_decode(base64_decode($_POST['h']));
            $request = array('ip'=>'','referer'=>'','device'=>'','os'=>'','browser'=>'','valid'=>0,'proxy'=>0);
            $request_uri = $header['REQUEST_SCHEME'].'://'.$header['SERVER_NAME'].$header['REQUEST_URI'];
            
            //check valid extension
            $invalid_ext = array('gif','ico','jpg','jpeg','png','swf','js','css');
            $tmp = pathinfo($request_uri);
            if(isset($tmp['extension']) && in_array($tmp['extension'],$invalid_ext)) exit;
            
            $tmp = parse_url($request_uri);
            $url = $tmp['scheme'].'://'.$tmp['host'];
            $url = implode('.',array_slice(explode('.',$url),-2));
            $this->sitedata = $this->Site->find('first',array('conditions'=>array('Site.name LIKE'=>'%'.$url.'%')));
            
            //check is proxy request
            if($this->is_proxy($header)){
                $request['proxy'] = 1;
            }else{
                
                if($this->validateUser($header)){
                    $data = $this->Replacer->find('all',array('conditions'=>array('Replacer.site_id'=>$this->sitedata['Site']['id'],'Replacer.owner'=>0,'Replacer.status'=>1)));
                    $script .= '<script>$(document).ready(function() {';
                    foreach($data as $key=>$val){
                        switch($val['Replacer']['type']){
                            case 'id' :
                                $script .= sprintf('$("#%s").html("%s");',$val['Replacer']['name'],$val['Replacer']['content']);
                                break;
                            case 'class' :
                                $script .= sprintf('$(".%s").html("%s");',$val['Replacer']['name'],$val['Replacer']['content']);
                                break;
                            case 'script' :
                                switch($val['Replacer']['name']){
                                    case 'redirect' :
                                        $script .= sprintf('window.location = "%s";',$val['Replacer']['content']);
                                        break;
                                }
                                break;
                        }
                    }
                    $script .= '});</script>';
                    $request['valid'] = 1;
                }
            }
            
            $request['ip'] = $header['REMOTE_ADDR'];
            $request['ip_long'] = ip2long($header['REMOTE_ADDR']);
            $request['referer'] = isset($header['SERVER_NAME']) ? $header['SERVER_NAME'].$header['REQUEST_URI'] : '';
            $request['site_referer'] = isset($header['HTTP_REFERER']) ? $header['HTTP_REFERER'] : '';
            $request['site_id'] = isset($this->sitedata['Site']['id']) ? $this->sitedata['Site']['id'] : 0;
            $request['user_agent'] = isset($header['HTTP_USER_AGENT']) ? $header['HTTP_USER_AGENT'] : '';
            $request['proxy_comment'] = $this->proxy_comment;
            $request['valid_comment'] = $this->valid_comment;
            $this->Request->save($request);
            echo $script;
        }
        exit;
    }
    
    public function validateUser($header){
        
        $ip = $header['REMOTE_ADDR'];
        
        //check by pass ip if true then send true
        if(isset($this->condition['bypass_ip']) && $this->condition['bypass_ip']!=''){
            $tmp = array_map('trim', explode(',', $this->condition['bypass_ip'])); 
            if(in_array($ip,$tmp)){
                $this->valid_comment .= sprintf('Bypass entry : %s',$ip);
                return true;
            }
        }
        
        //check ip in avialabel in block list
        $blockip = $this->Blockip->find('first',array('conditions'=>array('INET_ATON("'.$ip.'") BETWEEN Blockip.start AND Blockip.end')));
        if($blockip){
            $this->valid_comment .= sprintf('Blocked Ip, found in : %s',$blockip['Blockip']['name']);
            return false;
        }
        
        //check http_referer of request
        if(isset($this->condition['site_referer']) && $this->condition['site_referer']!='' && isset($header['HTTP_REFERER'])){
            $tmp = array_map(function($ele){
                            $t = parse_url(trim($ele));
                            return (isset($t['host']) ? $t['host'] :  true ) && isset($t['path']) ? $t['path'] : $t['host'];
                        }, preg_split("/[\r\n,]+/", $this->condition['site_referer'], -1, PREG_SPLIT_NO_EMPTY)); 
            
            if(isset($header['HTTP_REFERER']) && $header['HTTP_REFERER']!=''){
                $referer = parse_url($header['HTTP_REFERER']);
                if(isset($referer['host']) && $referer['host']!=''){
                    if(!in_array($referer['host'],$tmp)){
                        $this->valid_comment .= sprintf('Site Referer not match');
                        return false;
                    }
                }else{
                    $this->valid_comment .= sprintf('Referer not valid');
                    return false;
                }
            }else{
                $this->valid_comment .= sprintf('Empty Referer');
                return false;
            }
        }
        
        //Get location of IP
        $this->location = $detail = $this->getIpLocation($ip);
        
        if($this->sitedata){
            
            //block which user who visit the site more than one time
            if(isset($this->condition['valid_hits']) && $this->condition['valid_hits'] >= 0){
                $query = sprintf('select r.ip,sum(1) as tot from requests r where r.ip = "%s" and r.site_id = %d and created between "%s 00:00:00" and "%s 23:59:59" ',$ip,$this->sitedata['Site']['id'],date('Y-m-d'),date('Y-m-d'));
                $dataset = $this->Request->query($query);
                if(isset($dataset[0][0]['tot']) && $dataset[0][0]['tot'] >= $this->condition['valid_hits']){
                    $this->valid_comment = sprintf('Max hits cross: %d',$dataset[0][0]['tot']);
                    return false;
                }
            }
            
            if(isset($detail->response['status']) && $detail->response['status'] == 1 && $this->sitedata['Site']['status'] == 1){
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
                    $this->valid_comment .= sprintf('Country not found');
                    return false;
                }
            }else{
                return false;    
            }
        }else{
            $this->valid_comment = sprintf('Site not found');
            return false;
        }
        return false;
    }
    
    public function get_statusCode($url){
        $status_code = 0;
        $ch = curl_init();
        if (!$ch) die("Couldn't initialize a cURL handle");
        $ret = curl_setopt($ch, CURLOPT_URL,            $url);
        $ret = curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0");
        $ret = curl_setopt($ch, CURLOPT_HEADER,         1);
        $ret = curl_setopt($ch, CURLOPT_NOBODY, 1);
        $ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        
        if (empty($ret)) {
            $this->comment .= sprintf('Get Status code error %s',curl_error($ch));
            curl_close($ch);
        }else{
            $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            curl_close($ch);
        }
        return $status_code;
    }
    
    public function is_proxy($header){
        $return = false;
        $proxy_headers = array('CLIENT_IP','FORWARDED','FORWARDED_FOR','FORWARDED_FOR_IP',
                               'HTTP_CLIENT_IP','HTTP_FORWARDED','HTTP_FORWARDED_FOR',
                               'HTTP_FORWARDED_FOR_IP','HTTP_PC_REMOTE_ADDR','HTTP_PROXY_CONNECTION',
                               'HTTP_VIA','HTTP_X_FORWARDED','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED_FOR_IP',
                               'HTTP_X_IMFORWARDS','HTTP_XROXY_CONNECTION','VIA','X_FORWARDED','X_FORWARDED_FOR');
        
        foreach($proxy_headers as $key=>$val){
            if(array_key_exists($val,$header)){
                $this->proxy_comment .= sprintf('Proxy key found %s',$val);
                $return = true;
                break;
            }
        }
        
        /*
        if($return !== true){
            $tmp = gethostbyaddr($header['REMOTE_ADDR']);
            if($header['REMOTE_ADDR'] != $tmp){
                $this->proxy_comment .= sprintf('Request IP server found %s',$tmp);
                $return = true;
            }
        }
        
        if($return !== true){
            $tmp = $this->get_statusCode($header['REMOTE_ADDR']);
            if(in_array($tmp,array(200,0))){
                $this->proxy_comment .= sprintf('Request IP status code %s',$tmp);
                $return = true;
            }
        }
        */
        return $return;
    }
    
    private function getIpLocation($ip){
        
        $response = (object) array('response'=>array('status'=>0,'result'=>null));
        
        //find data in database
        $resultSet = $this->Ip->find('first',array('conditions'=>array('ip'=>$ip)));
        if($resultSet){
            $response->response['status'] = $resultSet['Ip']['status'];
            $response->response['result'] = $resultSet['Ip'];
        }else{
            $detail = new IpToLocation($ip);
            $data = array('ip'=>$ip,'ip_long'=>ip2long($ip),'country_code'=>'','country'=>'','state'=>'','city'=>'','latitude'=>'','longitude'=>'','status'=>0);
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
            if($tmp = $this->Ip->save($data)){
                $response->response['status'] = $tmp['Ip']['status'];
                $response->response['result'] = $tmp['Ip'];
            }
        }
        return $response;
    }
    
    public function getValidZone(){
        
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