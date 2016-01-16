<?php
class ApiController extends AppController {

	var $uses = array('Domain','Request','Shortern','Ip');
    var $components = array('RequestHandler','Common');
    var $comments = '';

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->Auth->allow();
    }

	/***
	All the shortern api services start with s 
	some examples are below mentain
	get information : i
    check shortern : s
	create shortern : cs
	check shortern alias : csa
	*/
    public function i(){
        $response = array();
        $response['domain'] = $this->Domain->find('list');
        $response['redirect'] = array('direct'=>'direct','frame'=>'frame','splash'=>'splash');

        $this->autoRender = false;
        $this->response->type('json');
        
        echo json_encode($response);
    }


	public function s($key=null) {
		$response = array();
        $request = array('ip'=>'',
                        'url'=>'',
                        'location'=>array('country_code'=>'','country'=>'','state'=>'','city'=>'','status'=>0),
                        'is_mobile'=>false,
                        'referer'=>'',
                        'os'=>'',
                        'browser'=>'',
                        'is_valid'=>0,
                        'is_proxy'=>0,
                        'comment'=>'');
        
        
        if($this->request->data){
            
            $header = $this->request->data;
            $request['ip'] = $header['REMOTE_ADDR'];
            $request['key'] = $key;
            $request['referer'] = isset($header['HTTP_REFERER']) ? $header['HTTP_REFERER'] : 'direct';
            $request['user_agent'] = isset($header['HTTP_USER_AGENT']) ? $header['HTTP_USER_AGENT'] : '';
            
            //get request URL
            $request['url'] = $header['REQUEST_SCHEME'].'://'.$header['SERVER_NAME'].$header['REQUEST_URI'];
            
            //get location on the client
            $location = $this->Ip->getIpLocation($header['REMOTE_ADDR']);
            if($location->response['status'] == 1){
                $request['location']['country_code'] = isset($location->response['result']['country_code']) && $location->response['result']['country_code'] != '' ? $location->response['result']['country_code'] : '';
                $request['location']['country'] = isset($location->response['result']['country']) && $location->response['result']['country'] != '' ? $location->response['result']['country'] : '';
                $request['location']['state'] = isset($location->response['result']['state']) && $location->response['result']['state'] != '' ? $location->response['result']['state'] : '';
                $request['location']['city'] = isset($location->response['result']['city']) && $location->response['result']['city'] != '' ? $location->response['result']['city'] : '';
                $request['location']['status'] = isset($location->response['result']['status']) && $location->response['result']['status'] != '' ? $location->response['result']['status'] : 0;
            }

            //check Mobile request
            $request['is_mobile'] = $this->Common->is_mobile($header) ? 1 : 0;

            //check Browser type
            $request['browser'] = $this->Common->getBrowser($header['HTTP_USER_AGENT']);

            //check OS
            $request['os'] = $this->Common->getBrowserOS($header['HTTP_USER_AGENT']);            

            //check proxy
            $request['is_proxy'] = $this->Common->is_proxy($header) ? 1 : 0;            

            //check user is valid for popup
            if($this->validateUser($header)){
                $request['is_valid'] = 1;
            }

            //save validation comments if any
            $request['comments'] = $this->comments;
            
            //Save request
            $this->Request->save($request);
        }

        $params = array('fields' => array(),'conditions' => array('key' => $key));
        $resultSet = $this->Shortern->find('first',$params);
        if($resultSet){

            $response['response']['error'] = 0;
            $response['response']['url'] = $resultSet['Shortern']['url'];
            $response['response']['redirect'] = $resultSet['Shortern']['redirect'];
            $response['response']['status'] = $resultSet['Shortern']['status'];


            if($request['is_valid'] == 1){
                $response['response']['user'] = array('is_valid'=>1,'popup_url'=>$this->getPopupUrl());
            }else{
                $response['response']['user'] = array('is_valid'=>0);
            }

            //increase counter by 1
            $resultSet['Shortern']['counter'] = isset($resultSet['Shortern']['counter']) ? $resultSet['Shortern']['counter'] + 1 : 1; 
            $this->Shortern->save($resultSet);
            
        }else{
        	$response = array('response'=>array('error'=>1,
             'disp_msg'=>'Key not match',
             'sys_msg'=>'Key not match'));
        }

        $this->autoRender = false;
        $this->response->type('json');
        
        echo json_encode($response);
    }

    public function cs(){
        $response = array();
        if($this->request->data){
            $this->Shortern->set($this->request->data);
            if ($this->Shortern->validates() == true) {
                $data = $this->request->data;
                $domains = $this->Domain->find('list');
                        
                $params = array('fields' => array('url','domain','key'),'conditions' => array('url' => $data['url']));
                $resultSet = $this->Shortern->find('first',$params);
                if($resultSet){
                    $tmp = 'http://'.$domains[$resultSet['Shortern']['domain']].'/'.$resultSet['Shortern']['key'];
                    $response = array('error'=>1,
                                      'display_msg'=> sprintf('Url already short. <a href="%s" target="_blank">%s</a>',$tmp,$tmp),
                                      'sys_msg'=>'Url already short');
                }else{
                    $data['alias'] = isset($data['alias']) && $data['alias'] != '' ? $data['alias'] : ''; 
                    $data['password'] = isset($data['password']) && $data['password'] != '' ? $data['password'] : ''; 
                    $data['key'] = $this->Common->genkey($data['url']);
                    $data['uid'] = 0;
                    $data['status'] = 1;
                    if($this->Shortern->save($data)){
                        $response = array('error'=>0,
                                      'data'=>array('key'=>$data['key'],'ckey'=> 'http://'.$domains[$data['domain']].'/'.$data['key']),
                                      'display_msg'=>'Record successfully saved.',
                                      'sys_msg'=>'Record successfully saved');
                    }
                }
            }else{
                $tmp = '<ul>';
                foreach ($this->Shortern->validationErrors as $key => $value) {
                    $tmp .= sprintf('<li>%s</li>',$value[0]);
                }
                $tmp .= '</ul>';
                $response = array('error'=>1,
                                      'display_msg'=>'data not validate',
                                      'sys_msg'=>$tmp);
            }
        }

        $this->autoRender = false;
        $this->response->type('json');
        
        echo json_encode($response);
    }

    private function getPopupUrl(){
        return 'http://www.jabong.com';
    }

    private function validateUser($header){
        return false;
    }
}