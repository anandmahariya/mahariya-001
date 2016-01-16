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
    
    function getBrowser($u_agent){
        
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
            //'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            //'pattern'    => $pattern
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

    function is_proxy($header){
        $proxy_headers = array(
                        'CLIENT_IP',
                        'FORWARDED',
                        'FORWARDED_FOR',
                        'FORWARDED_FOR_IP',
                        'HTTP_CLIENT_IP',
                        'HTTP_FORWARDED',
                        'HTTP_FORWARDED_FOR',
                        'HTTP_FORWARDED_FOR_IP',
                        'HTTP_PC_REMOTE_ADDR',
                        'HTTP_PROXY_CONNECTION',
                        'HTTP_VIA',
                        'HTTP_X_FORWARDED',
                        'HTTP_X_FORWARDED_FOR',
                        'HTTP_X_FORWARDED_FOR_IP',
                        'HTTP_X_IMFORWARDS',
                        'HTTP_XROXY_CONNECTION',
                        'VIA',
                        'X_FORWARDED',
                        'X_FORWARDED_FOR'
                    );
        
        foreach($proxy_headers as $key=>$val){
            if(array_key_exists($val,$header)){
                return true;
                break;
            }
        }
        return false;
    }

    public function is_mobile($header){
        $useragent = $header['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
            $this->comments .= sprintf('<li>Mobile Browser dectect</li>');
            return true;    
        }
        return false;
    }
}