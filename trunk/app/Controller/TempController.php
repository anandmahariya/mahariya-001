<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class TempController extends AppController {
    
    public $uses = array('Site','Replacer','ValidZone','AdminZone','Country','State','City','Ip','Request');
    public $components = array('RequestHandler','Common');
    
    public function index() {
        $query = sprintf('SELECT SUM(1) as `tot`,SUM(IF(site_referer REGEXP "google.com",1,0)) as `vr`  FROM `requests` ',date('Y-m-d'),date('Y-m-d'));
        $dataset = $this->Request->query($query);
        
        echo '<pre>';print_r($dataset);echo '</pre>';
        exit;
        
    }
    
    public function browserGraph(){
        $query = sprintf('SELECT r.user_agent FROM `requests` r  ',date('Y-m-d'),date('Y-m-d'));
        $dataset = $this->Request->query($query);
        
        $data = array();
        foreach($dataset as $key=>$val){
            $tmp = $this->Common->getBrowserOS($val['r']['user_agent']);
            $preCount = isset($data[$tmp['os_platform']][$tmp['browser']]) ? $data[$tmp['os_platform']][$tmp['browser']] : 0 ;
            $data[$tmp['os_platform']][$tmp['browser']] = $preCount + 1;
        }
        
        $result = array();
        foreach($data as $key=>$val){
            $result[$key] = array_sum($val);
        }
        
        $response = array('total'=>array_sum($result),'data'=>$result);
        echo '<pre>';print_r($response);echo '</pre>';
        exit;
    }
    
    
    function getBrowserOS($user_agent) { 
        $browser        =   "Unknown Browser";
        $os_platform    =   "Unknown OS Platform";

        // Get the Operating System Platform
        if (preg_match('/windows|win32/i', $user_agent)) {
            $os_platform    =   'Windows';
            /*
            if (preg_match('/windows nt 6.2/i', $user_agent)) {
                $os_platform    .=  " 8";
            } else if (preg_match('/windows nt 6.1/i', $user_agent)) {
                $os_platform    .=  " 7";
            } else if (preg_match('/windows nt 6.0/i', $user_agent)) {
                $os_platform    .=  " Vista";
            } else if (preg_match('/windows nt 5.2/i', $user_agent)) {
                $os_platform    .=  " Server 2003/XP x64";
            } else if (preg_match('/windows nt 5.1/i', $user_agent) || preg_match('/windows xp/i', $user_agent)) {
                $os_platform    .=  " XP";
            } else if (preg_match('/windows nt 5.0/i', $user_agent)) {
                $os_platform    .=  " 2000";
            } else if (preg_match('/windows me/i', $user_agent)) {
                $os_platform    .=  " ME";
            } else if (preg_match('/win98/i', $user_agent)) {
                $os_platform    .=  " 98";
            } else if (preg_match('/win95/i', $user_agent)) {
                $os_platform    .=  " 95";
            } else if (preg_match('/win16/i', $user_agent)) {
                $os_platform    .=  " 3.11";
            }
            */
        } else if (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $os_platform    =   'Mac';
            if (preg_match('/macintosh/i', $user_agent)) {
                $os_platform    .=  " OS X";
            } else if (preg_match('/mac_powerpc/i', $user_agent)) {
                $os_platform    .=  " OS 9";
            }
        } else if (preg_match('/linux/i', $user_agent)) {
            $os_platform    =   "Linux";
        }
        
        // Override if matched
        if (preg_match('/iphone/i', $user_agent)) {
            $os_platform    =   "iPhone";
        } else if (preg_match('/android/i', $user_agent)) {
            $os_platform    =   "Android";
        } else if (preg_match('/blackberry/i', $user_agent)) {
            $os_platform    =   "BlackBerry";
        } else if (preg_match('/webos/i', $user_agent)) {
            $os_platform    =   "Mobile";
        } else if (preg_match('/ipod/i', $user_agent)) {
            $os_platform    =   "iPod";
        } else if (preg_match('/ipad/i', $user_agent)) {
            $os_platform    =   "iPad";
        }
        
        // Get the Browser
        if (preg_match('/msie/i', $user_agent) && !preg_match('/opera/i', $user_agent)) { 
            $browser        =   "Internet Explorer"; 
        } else if (preg_match('/firefox/i', $user_agent)) { 
            $browser        =   "Firefox";
        } else if (preg_match('/chrome/i', $user_agent)) { 
            $browser        =   "Chrome";
        } else if (preg_match('/safari/i', $user_agent)) { 
            $browser        =   "Safari";
        } else if (preg_match('/opera/i', $user_agent)) { 
            $browser        =   "Opera";
        } else if (preg_match('/netscape/i', $user_agent)) { 
            $browser        =   "Netscape"; 
        }
            
        // Override if matched
        if ($os_platform == "iPhone" || $os_platform == "Android" || $os_platform == "BlackBerry" || $os_platform == "Mobile" || $os_platform == "iPod" || $os_platform == "iPad") { 
            if (preg_match('/mobile/i', $user_agent)) {
                $browser    =   "Handheld Browser";
            }
        }
        
        // Create a Data Array
        return array(
            'browser'       =>  $browser,
            'os_platform'   =>  $os_platform
        );
    }

}