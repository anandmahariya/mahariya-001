<?php
App::import('Vendor', 'getid3/getid3');
class CommonComponent extends Component {

    public function is_ffmpeg(){
        $ffmpeg = trim(shell_exec('which ffmpeg'));
        if(empty($ffmpeg))
            return false;
        else
            return true;
    }
    function randColor( $numColors ) {
        $chars = "ABCDEF0123456789";   
        $size = strlen( $chars );
        $str = array();
        for( $i = 0; $i < $numColors; $i++ ) {
            $tmp = '#';
            for( $j = 0; $j < 6; $j++ ) {
                $tmp .= $chars[ rand( 0, $size - 1 ) ];
            }
            $str[$i] = $tmp;
        }
        return $str;
    }
       
    function getFileInfo($absolute_path = null){
        if(file_exists($absolute_path)){
            $getID3 = new getID3;
            return $getID3->analyze($absolute_path);
        }else{
            return false;
        }
    }
    
    public function mysqlDate($date,$format,$time = 'start'){
        $retval = false;
        switch($format){
            case 'dd/mm/yy' :
                $tmp = explode('/',$date);
                $retval = $time == 'end' ?  date('Y/m/d H:i:s',mktime(23,59,59,$tmp[1],$tmp[0],$tmp[2])) : date('Y/m/d H:i:s',mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2])) ;
            break;    
        }
        return $retval;
    }
    
    public function get_data($url,$referer = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        
        if($referer !== false)
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    function getBrowser(){
        
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
    
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
       
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent)){
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent)){
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent)){
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent)){
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent)){
            $bname = 'Netscape';
            $ub = "Netscape";
        }
       
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
       
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
       
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
       
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    
    function getAddrByHost($host,$loop = 0){
        $tmp = $this->getAddrByHost_core($host);
        if(in_array($tmp,array('NXDOMAIN','SERVFAIL','empty'))){
            if($loop >= 2){
                return $tmp;
            }else{
                return $this->getAddrByHost($host,$loop + 1);
            }
        }else{
            return $tmp;
        }
    }
    
    function getAddrByHost_core($host) {
        $query = `host $host`;
        if(preg_match('/pointer(.*)/', $query, $matches)){
            return trim($matches[1]);
        }elseif(preg_match('/(NXDOMAIN)/', $query, $matches)){
            return trim($matches[1]);
        }elseif(preg_match('/(SERVFAIL)/', $query, $matches)){
            return trim($matches[1]);
        }else{
            return 'empty';
        }
    }
    
    function array2Html($array, $table = true){
        $out = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!isset($tableHeader)) {
                    $tableHeader =
                        '<th>' .
                        implode('</th><th>', array_keys($value)) .
                        '</th>';
                }
                array_keys($value);
                $out .= '<tr>';
                $out .= $this->array2Html($value, false);
                $out .= '</tr>';
            } else {
                $out .= "<td>$value</td>";
            }
        }
    
        if ($table) {
            return '<table class="table table-bordered">' . $tableHeader . $out . '</table>';
        } else {
            return $out;
        }
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
        
        //Some additional operating system like googlebot baiduspider
        if (preg_match('/Googlebot/i', $user_agent) || preg_match('/AdsBot/i', $user_agent)) {
            $os_platform    =   "Googlebot";
        }else if (preg_match('/Baiduspider/i', $user_agent)) {
            $os_platform    =   "Baiduspider";
        }else if (preg_match('/prodvigatorBot/i', $user_agent)) {
            $os_platform    =   "prodvigatorBot";
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

    function genkey($url){
        return substr(hash('crc32b',$url),0,6);
    }
    
}