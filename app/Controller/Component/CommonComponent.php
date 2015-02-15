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
}