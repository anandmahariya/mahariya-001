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
    
    function getVideoImage($source = null,$duration = '0.020'){
        if($this->is_ffmpeg()){
            $target = WWW_ROOT.'thumbs/'.uniqid().'.jpg';
            $command = sprintf('ffmpeg -i %s -ss %s -f image2 -vframes 1 %s',$source,$duration,$target);
            $result = trim(shell_exec($command));
            if(file_exists($target)){
                $getID3 = new getID3;
                return $getID3->analyze($target);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function getPromoVideo($source = null,$start_time = null,$duration = null){
        if($this->is_ffmpeg()){
            $target = WWW_ROOT.'uploads/videos/'.uniqid().'.mp4';
            $command = sprintf('ffmpeg -ss %d -i %s -t %d -vcodec copy -acodec copy -y %s',$start_time,$source,$duration,$target);
            $result = trim(shell_exec($command));
            if(file_exists($target)){
                $getID3 = new getID3;
                return $getID3->analyze($target);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function getFileInfo($absolute_path = null){
        if(file_exists($absolute_path)){
            $getID3 = new getID3;
            return $getID3->analyze($absolute_path);
        }else{
            return false;
        }
    }
    
    function getPromoImage($absolute_path = null){
        $slice = 9;
        $tmpDir = WWW_ROOT.'tmp/';
        $files = array();
        $absolute_path = '/var/www/newsnation/app/webroot/uploads/videos/53b15cd6360ad.mp4';
        $fileInfo = $this->getFileInfo($absolute_path);
        if($fileInfo['filesize'] > 0){
            $command = sprintf('ffmpeg -ss 00:00:10 -i %s  -r %f -vframes %d -s 160*120  -f image2 %simages1%%03d.png',$absolute_path,($slice + 1) / ($fileInfo['playtime_seconds'] +  10),$slice,$tmpDir);
            shell_exec($command);
            if ($handle = opendir($tmpDir)) {
                $all = new Imagick();
                while (false !== ($entry = readdir($handle))) {
                    if($entry != '.' && $entry != '..'){
                        $files[] = $tmpDir.$entry;
                    }
                }
                
                //Sorting process
                sort($files);
                foreach($files as $file){
                    $im = new Imagick($file);
                    $all->addImage($im);
                }
                $all->resetIterator();
                $combined = $all->appendImages(true);
                $combined->setImageFormat("jpg");
                file_put_contents($tmpDir.uniqid().'.jpg',$combined);    
            }
        }
        exit;
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
    
    public function gatewayResults($url, $proxy) {
            $types = array(
                    'http',
                    'socks4',
                    'socks5'
            );
    
            $url = curl_init($url);
    
            curl_setopt($url, CURLOPT_PROXY, $proxy);
    
            foreach ($types as $type) {
                    switch ($type) {
                            case 'http':
                                    curl_setopt($url, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                                    break;
                            case 'socks4':
                                    curl_setopt($url, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                                    break;
                            case 'socks5':
                                    curl_setopt($url, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                                    break;
                    }
    
                    curl_setopt($url, CURLOPT_TIMEOUT, 10);
                    curl_setopt($url, CURLOPT_RETURNTRANSFER, 1);
    
                    $resultsQuery = explode('---', curl_exec($url));
    
                    if (!empty($resultsQuery)) {
                            break;
                    }
            }
    
            $results = array();
    
            foreach ($resultsQuery as $result) {
                    if (!empty($result)) {
                            $split = explode('--', $result);
    
                            if (!empty($split[1])) {
                                    $results[$split[0]] = $split[1];
                            }
                    }
            }
    
            curl_close($url);
            unset($url);
    
            return $results;
    }
    
    public function checkAnonymity($server = array()) {
	$realIp = $_SERVER['REMOTE_ADDR'];
        $level = 'transparent';

	if (!in_array($realIp, $server)) {
		$level = 'anonymous';

		$proxyDetection = array(
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_PROXY_ID',
			'HTTP_VIA',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP',
			'HTTP_FORWARDED_FOR_IP',
			'VIA',
			'X_FORWARDED_FOR',
			'FORWARDED_FOR',
			'X_FORWARDED FORWARDED',
			'CLIENT_IP',
			'FORWARDED_FOR_IP',
			'HTTP_PROXY_CONNECTION',
			'HTTP_XROXY_CONNECTION'
		);

		if (!array_intersect(array_keys($server), $proxyDetection)) {
			$level = 'elite';
		}
	}

	return $level;
    }
}