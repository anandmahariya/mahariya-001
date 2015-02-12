<?php
App::import('Vendor', 'iptolocation/ipInfo.inc');
class IpToLocation{
    
    var $response = array();
    
    public function __construct($ip = null){
        
        //$this->resultStruct = array_flip(array('ip','city','state','state_code','country','country_code','zipcode','timezone','latitude','longitude'));
        
        $valid = filter_var($ip, FILTER_VALIDATE_IP);
        if($valid){
            $function_array = array('ip_api');
            $func = $function_array[rand(0,count($function_array) - 1)];
            
            $tmp = $this->{$func}($ip);
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
    
    /****
     *API URL : http://www.telize.com
     */
    private function telize($ip){
        $url = sprintf('http://www.telize.com/geoip/%s',$ip);
        $content = $this->getContent($url);
        $data = json_decode($content['content']);
        
        echo '<pre>';print_r($data);echo '</pre>';
        
        if(isset($data->status) && $data->status != ''){
            switch($data->status){
                case 'success' :
                    return array('ip'=>$data->ip,
                                        'city'=>$data->city,
                                        'state'=>$data->regionName,
                                        'state_code'=>$data->region,
                                        'country'=>$data->country,
                                        'country_code'=>$data->country_code,
                                        'zipcode'=>$data->zip,
                                        'timezone'=>$data->timezone,
                                        'latitude'=>$data->lat,
                                        'longitude'=>$data->lon);
                    break;
                case 'fail' :
                    return false;
                    break;
            }
        }
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
                                        'longitude'=>$data->lon);
                    break;
                case 'fail' :
                    return false;
                    break;
            }
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