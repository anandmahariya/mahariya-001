<?php
class ApiController extends AppController {

	var $uses = array('Domain','Request','Shortern','Ip');
    var $components = array('RequestHandler','Common');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->Auth->allow();
    }

	/***
	All the shortern api services start with s 
	some examples are below mentain
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
        $params = array('fields' => array(),'conditions' => array('key' => $key));
        $resultSet = $this->Shortern->find('first',$params);
        if($resultSet){

            $response = array('response'=>array(
                'error'=>0,
                'url'=>$resultSet['Shortern']['url'],
                'status'=>$resultSet['Shortern']['status'],
                'redirect'=>$resultSet['Shortern']['redirect'],
                'user'=>array('valid'=>1,
                 'popup_url'=>'http://www.jabong.com'
                 )
                )
            );

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

    function cs(){
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
}