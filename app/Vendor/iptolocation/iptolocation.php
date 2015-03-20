<?php
App::import('Vendor', 'iptolocation/ipInfo.inc');
class IpToLocation{
    
    var $response = array();
    
    public function __construct($ip = null){
        $valid = filter_var($ip, FILTER_VALIDATE_IP);
        if($valid){
            $tmp = $this->getLocation($ip);
            if($tmp !== false){
                $this->response['status'] = 1;
                $this->response['result'] = $tmp;
            }else{
                $this->response['status'] = 0;
                $this->response['error'][] = 'Valid IP address but not process';
            }
        }else{
            $this->response['status'] = 0;
            $this->response['error'][] = 'Invalid IP address';
        }
        return $this->response;
    }
    
    private function getLocation($ip,$count = 0){
        $function_array = array('ipinfodb','ip_api','telize');
        $func = $function_array[rand(0,count($function_array) - 1)];
        $tmp = $this->{$func}($ip);
        if($tmp['city'] != '' && $tmp['state'] != '' && $tmp['country_code'] != ''){
            return $tmp;
        }else{
            if($count <= 2)
                return $this->getLocation($ip,$count + 1);
            else
                return false;
        }
    }
     
    /****
     *key : 8de537c2470c2992f526e1699a62f951a92bdba8d6c0a47d6f1eda7b85bf6414
     *http://api.ipinfodb.com/v3/ip-city/?key=8de537c2470c2992f526e1699a62f951a92bdba8d6c0a47d6f1eda7b85bf6414&ip=74.125.45.100&format=json
     *IPinfodb.com
     */
    private function ipinfodb($ip){
        $key = '8de537c2470c2992f526e1699a62f951a92bdba8d6c0a47d6f1eda7b85bf6414';
        $url = sprintf('http://api.ipinfodb.com/v3/ip-city/?key=%s&ip=%s&format=json',$key,$ip);
        $content = $this->getContent($url);
        $data = json_decode($content['content']);
        if(isset($data->statusCode) && $data->statusCode == 'OK'){
            return array('ip'=>$ip,
                                        'city'=>$data->cityName,
                                        'state'=>$data->regionName,
                                        'state_code'=>'',
                                        'country'=>$data->countryName,
                                        'country_code'=>$data->countryCode,
                                        'zipcode'=>$data->zipCode,
                                        'timezone'=>$data->timeZone,
                                        'latitude'=>$data->latitude,
                                        'longitude'=>$data->longitude,
                                        'function'=>'ipinfodb');   
        }
        return false;
    }
    
    /***
     * API URL : http://ip-api.com
     */
    private function ip_api($ip){
        
        $url = sprintf('http://ip-api.com/json/%s',$ip);
        $content = $this->getContent($url);
        $data = json_decode($content['content']);
        if(isset($data->status) && $data->status != ''){
            switch($data->status){
                case 'success' :
                    return array('ip'=>$data->query,
                                        'city'=>$data->city,
                                        'state'=>$data->regionName,
                                        'state_code'=>$data->region,
                                        'country'=>$data->country,
                                        'country_code'=>$data->countryCode,
                                        'zipcode'=>$data->zip,
                                        'timezone'=>$data->timezone,
                                        'latitude'=>$data->lat,
                                        'longitude'=>$data->lon,
                                        'function'=>'ip_api');
                    break;
            }
        }
        return false;
    }
    
    /****
     *API URL : http://www.telize.com
     */
    private function telize($ip){
        $url = sprintf('http://www.telize.com/geoip/%s',$ip);
        $content = $this->getContent($url);
        $data = json_decode($content['content']);
        if(isset($data->country) && $data->country!='' &&
           isset($data->region) && $data->region!='' &&
           isset($data->city) && $data->city!=''){
            return array('ip'=>$ip,
                        'city'=>$data->city,
                        'state'=>$data->region,
                        'state_code'=>$data->region_code,
                        'country'=>$data->country,
                        'country_code'=>$data->country_code,
                        'zipcode'=>'',
                        'timezone'=>$data->timezone,
                        'latitude'=>$data->latitude,
                        'longitude'=>$data->longitude,
                        'function'=>'telize');       
        }
        return false;
    }
    
    /****
     *API URL : http://api.db-ip.com
     *Key : d3448bb1d1a79d7627bf3b63bcace48da437be12
     */
    private function dp_api($ip){
        $key = 'd3448bb1d1a79d7627bf3b63bcace48da437be12';
        $url = sprintf('http://api.db-ip.com/addrinfo?addr=%s&api_key=%s',$ip,$key);
        $content = $this->getContent($url);
        $data = json_decode($content['content']);
        if(isset($data->country) && $data->country!='' &&
           isset($data->stateprov) && $data->stateprov!='' &&
           isset($data->city) && $data->city!=''){
            return array('ip'=>$ip,
                                        'city'=>$data->city,
                                        'state'=>$data->stateprov,
                                        'state_code'=>'',
                                        'country'=>'',
                                        'country_code'=>$data->country,
                                        'zipcode'=>'',
                                        'timezone'=>'',
                                        'latitude'=>'',
                                        'longitude'=>'');       
        }
        return false;
    }
    
    
    private function getContent($url){
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );
    
        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
    
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
}