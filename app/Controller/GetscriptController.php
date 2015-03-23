<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class GetscriptController extends AppController {
    
    public $sitedata = array();
    public $location = array();
    public $condition = array();
    public $comments = '';
    public $mobile = 0;
    public $proxy = 0;
    public $invalid_as = 0;
    public $components = array('RequestHandler','Common');
    public $uses = array('Site','Replacer','ValidZone','RestrictedZone','AdminZone',
                         'Country','State','City','Ip','Request','Option','Blockip','Blockas');
    
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
            
            $request = array('ip'=>'','port'=>0,'referer'=>'','device'=>'','os'=>'',
                             'browser'=>'','valid'=>0,'proxy'=>0,'mobile'=>0,'comments'=>'');
            
            $request_uri = $header['REQUEST_SCHEME'].'://'.$header['SERVER_NAME'].$header['REQUEST_URI'];
            
            //get request ip
            $tmp = parse_url($request_uri);
            $url = $tmp['scheme'].'://'.$tmp['host'];
            $url = implode('.',array_slice(explode('.',$url),-2));
            $this->sitedata = $this->Site->find('first',array('conditions'=>array('Site.name LIKE'=>'%'.$url.'%')));
            
            //check valid extension
            if($this->is_validExt($request_uri) === false){
                exit;
            }
            
            //Get location 
            $this->location = $this->getIpLocation($header['REMOTE_ADDR']);
            
            if($this->validateUser($header)){
                $data = $this->Replacer->find('all',array('conditions'=>array('Replacer.site_id'=>$this->sitedata['Site']['id'],'Replacer.owner'=>0,'Replacer.status'=>1)));
                if($data){
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
                }
                $request['valid'] = 1;
            }
            
            $request['ip'] = $header['REMOTE_ADDR'];
            $request['port'] = $header['SERVER_PORT'];
            $request['ip_long'] = ip2long($header['REMOTE_ADDR']);
            $request['referer'] = isset($header['SERVER_NAME']) ? $header['SERVER_NAME'].$header['REQUEST_URI'] : '';
            $request['site_referer'] = isset($header['HTTP_REFERER']) ? $header['HTTP_REFERER'] : '';
            $request['site_id'] = isset($this->sitedata['Site']['id']) ? $this->sitedata['Site']['id'] : 0;
            $request['user_agent'] = isset($header['HTTP_USER_AGENT']) ? $header['HTTP_USER_AGENT'] : '';
            $request['mobile'] = $this->mobile;
            $request['proxy'] = $this->proxy;
            $request['invalid_as'] = $this->invalid_as;
            $request['comments'] = sprintf('<ul>%s</ul>',$this->comments);
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
                $this->comments .= sprintf('<li>Bypass entry : %s</li>',$ip);
                return true;
            }
        }
        $this->comments .= sprintf('<li>Bypass : Pass</li>');
        
        //check is mobile
        if($this->is_mobile($header)){
            $this->mobile = 1;
            return false;
        }
        $this->comments .= sprintf('<li>Mobile : Pass</li>');
        
        //check http_referer of request
        if(isset($header['HTTP_REFERER']) && $header['HTTP_REFERER']!=''){
            
            $tmp = array_map(function($ele){
                            $t = parse_url(trim($ele));
                            return (isset($t['host']) ? $t['host'] :  true ) && isset($t['path']) ? $t['path'] : $t['host'];
                        }, preg_split("/[\r\n,]+/", $this->condition['site_referer'], -1, PREG_SPLIT_NO_EMPTY));
            
            $referer = parse_url($header['HTTP_REFERER']);
            if(isset($referer['host']) && $referer['host']!=''){
                if(!in_array($referer['host'],$tmp)){
                    $this->comments .= sprintf('<li>Site Referer not match</li>');
                    return false;
                }
            }else{
                $this->comments .= sprintf('<li>Referer not valid</li>');
                return false;
            }
        }else{
            $this->comments .= sprintf('<li>Empty Referer</li>');
            return false;
        }
        $this->comments .= sprintf('<li>Referer : Pass</li>');
        
        
        //check ip in invalid AS
        if(isset($this->location->response['status']) && $this->location->response['status'] == 1){
            if(isset($this->location->response['result']['as'])){
                $blockas = $this->Blockas->find('first',array('conditions'=>array('as'=>$this->location->response['result']['as'])));
                if($blockas){
                    $this->comments .= sprintf('<li>Blocked AS, found : %s</li>',$blockas['Blockas']['as']);
                    return false;
                }
            }
        }
        $this->comments .= sprintf('<li>Autonomous : Pass</li>');
        
        //check is proxy
        if($this->is_proxy($header)){
            $this->proxy = 1;
            return false;
        }
        $this->comments .= sprintf('<li>Proxy : Pass</li>');
        
        if($this->sitedata){
            
            //block which user who visit the site more than one time
            if(isset($this->condition['valid_hits']) && $this->condition['valid_hits'] >= 0){
                $query = sprintf('select r.ip,sum(1) as tot from requests r where r.ip = "%s" and r.site_id = %d and created between "%s 00:00:00" and "%s 23:59:59" ',$ip,$this->sitedata['Site']['id'],date('Y-m-d'),date('Y-m-d'));
                $dataset = $this->Request->query($query);
                if(isset($dataset[0][0]['tot']) && $dataset[0][0]['tot'] >= $this->condition['valid_hits']){
                    $this->comments .= sprintf('<li>Max hits cross: %d</li>',$dataset[0][0]['tot']);
                    return false;
                }
            }
            $this->comments .= sprintf('<li>Max click : Pass</li>');
            
            if($this->sitedata['Site']['status'] == 1){
                if(isset($this->location->response['status']) && $this->location->response['status'] == 1){
                    $result = $this->location->response['result'];
                    
                    //check which condition is apply
                    switch($this->condition['zone']){
                        case 'valid' :
                            return $this->is_validZone($result['country_code'],$result['state'],$result['city']);
                            break;
                        case 'restricted' :
                            return !$this->is_restrictedZone($result['country_code'],$result['state'],$result['city']);
                            break;
                    }
                }else{
                    $this->comments .= sprintf('<li>ip2location data not found</li>');
                    return false;    
                }
            }else{
                $this->comments .= sprintf('<li>Site %s is disable at %s</li>',$this->sitedata['Site']['name'],date('d, M Y h:i:s A'));
                return false;    
            }
        }else{
            $this->comments = sprintf('<li>Site not found</li>');
            return false;
        }
        return false;
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
                $this->comments .= sprintf('Proxy key found %s',$val);
                $return = true;
                break;
            }
        }
        
        /*
        if($return !== true){
            $ip = $header['REMOTE_ADDR'];
            $fp = @fsockopen($ip,$header['SERVER_PORT'], $errno, $errstr, 5);
            if ($fp){
                $tmp = $this->get_statusCode($header['REMOTE_ADDR']);
                if(in_array($tmp,array(200,0))){
                    $this->comments .= sprintf('Request %s status code : %s',$ip,$tmp);
                    $return = true;
                }
            }else{
                switch($errno){
                    case 111 :
                        $this->comments .= sprintf('Connection Refused from %s ',$ip);
                        $return = true;
                        break;
                }
            }
        }
        
        /*
        if($return !== true){
            $tmp = gethostbyaddr($header['REMOTE_ADDR']);
            if($header['REMOTE_ADDR'] != $tmp){
                $this->comments .= sprintf('Request IP server found %s',$tmp);
                $return = true;
            }
        }
        
        if($return !== true){
            $tmp = $this->get_statusCode($header['REMOTE_ADDR']);
            if(in_array($tmp,array(200,0))){
                $this->comments .= sprintf('Request IP status code %s',$tmp);
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
            
            $data = array('ip'=>$ip,'ip_long'=>ip2long($ip),'dns'=>'',
                          'country_code'=>'','country'=>'','state'=>'','city'=>'',
                          'latitude'=>'','longitude'=>'',
                          'as'=>'','registry'=>'','as_name'=>'',
                          'status'=>0);
            
            if($command = $this->get_originAS($ip)){
                $data = array_merge($data,$command);
            }
            
            //Get DNS of IP
            $tmp = $this->Common->getAddrByHost($ip);
            $data['dns'] = $tmp != '' ? $tmp : 'Empty';
            
            $detail = new IpToLocation($ip);
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
    
    /***************************************************** Final function *************************************************/
    
    public function get_originAS($ip,$loop = 0){
        $result = array();
        $kArray = array('as','IP','BGP Prefix','country_code','registry','Allocated','as_name');
        
        $command = sprintf('whois -h whois.cymru.com " -v %s"',$ip);
        $tmp = `$command`;
        
        if($tmp != ''){
            preg_match_all('/(.*)[\n]/i',$tmp,$data);
            if(isset($data[1][3]) && !empty($data[1][3])){
                $tmp = array_map('trim', explode('|', $data[1][3]));
                if(count($tmp) == 7){
                    foreach($kArray as $key=>$val){
                        $result[$val] = $tmp[$key];
                    }
                    return $result;
                }else{
                    $this->comments .= sprintf('<li>Get Origin data column not equal to 7</li>');
                    return false;
                }
            }else{
                $this->comments .= sprintf('<li>Get Origin date[1][3] not found</li>');
                return false;
            }
        }else{
            if($loop > 3){
                $this->comments .= sprintf('<li>Get Origin AS request exceed 3 ip %s </li>',$ip);
                return false;
            }else{
                return $this->get_originAS($ip,$loop + 1);
            }
        }
    }
    
    public function is_mobile($header){
        $useragent=$header['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
            $this->comments .= sprintf('<li>Mobile Browser dectect</li>');
            return true;    
        }
        return false;
    }
    
    public function is_validExt($url){
        $invalid_ext = array('gif','ico','jpg','jpeg','png','swf','js','css');
        $tmp = pathinfo($url);
        if(isset($tmp['extension']) && in_array($tmp['extension'],$invalid_ext)){
            return false;
        }
        return true;
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
    
    public function is_validZone($country_code,$state,$city){
        $validZones = $this->getValidZone();
        
        if(array_key_exists($country_code,$validZones)){
                                
            //State level check
            if(array_key_exists('*',$validZones[$country_code])){
                return true;
            }elseif(array_key_exists($state,$validZones[$country_code])){
                
                //City level check
                if(array_key_exists('*',$validZones[$country_code][$state])){
                    return true;
                }elseif(array_key_exists($city,$validZones[$country_code][$state])){
                    return true;    
                }else{
                    $this->comments .= sprintf('Country valid %s, %s state valid, but city %s not valid',$country_code,$state,$city);
                    return false;
                }
                
            }else{
                $this->comments .= sprintf('Country valid %s, but state %s not valid',$country_code,$state);
                return false;
            }
        }else{
            $this->comments .= sprintf('%s Country not found',$country_code);
            return false;
        }
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
    
    public function is_restrictedZone($country_code,$state,$city){
        $restrictedZones = $this->getRestrictedZone();
        //Country level check
        if($country_code != 'US'){
            $this->comments .= sprintf('Country in restricted zone : %s',$country_code);
            return true;
        }else{
            //State level check
            if(isset($restrictedZones[$country_code]) && array_key_exists('*',$restrictedZones[$country_code])){
                $this->comments .= sprintf('All States are in restricted zone in %s',$country_code);
                return true;
            }else{
                //City level check
                if(isset($restrictedZones[$country_code][$state]) && array_key_exists('*',$restrictedZones[$country_code][$state])){
                    $this->comments .= sprintf('All Cities are in restricted zone of %s, %s',$state,$country_code);
                    return true;
                }elseif(isset($restrictedZones[$country_code][$state]) && array_key_exists($city,$restrictedZones[$country_code][$state])){
                    $this->comments .= sprintf('%s City found restricted in zone  %s, %s',$city,$state,$country_code);
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    
    public function getRestrictedZone(){
        
        $response = array();
        $query = sprintf("select vz.id,c.code as country,
                         if(s.name is null,'*',s.name) as state,
                         if(ci.city is null,'*',ci.city) as city,
                         vz.status from restricted_zones vz
                         left join countries c on c.code = vz.country
                         left join states s on s.country_code = vz.country AND s.code = vz.state
                         left join cities ci on ci.id = vz.city
                         where vz.status = 1");
        
        $tmp = $this->RestrictedZone->query($query);
        
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
                    if(is_array($v)){
                        if(array_key_exists('*',$v)){
                            $response[$key][$k] = array('*'=>'all');
                        }
                    }
                }
            }
        }
        return $response;
    }
}