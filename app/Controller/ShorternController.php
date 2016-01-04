<?php
class ShorternController extends AppController {

	public $uses = array('Domain','Request','Shortern','Ip');
    var $components = array('RequestHandler');
    
    public function beforeFilter(){
        //$tmp = $this->Ip->getIpLocation('66.249.90.91');
        //echo '<pre>';print_r($tmp);echo '</pre>';
        //exit;
    }

    public function index() {
		$this->set('title','Shortern');
        $this->set('subtitle','urls');

        $condition = $paginate = array();
        
        if($this->request->data){
            if(isset($this->request->data['search']['url']) && $this->request->data['search']['url'] != ''){
                $searchStr = $this->request->data['search']['url'];
                $condition['Shortern.url'] = new MongoRegex("/$searchStr/i");
            }
            
            if(isset($this->request->data['search']['status']) && $this->request->data['search']['status'] != 0){
                $condition['Shortern.status'] = $this->request->data['search']['status'];
            }
        }

        $paginate['conditions'] = $condition;
        $paginate['fields'] = array('url','alias','redirect','password','key','domain','status');
        $paginate['limit'] = Configure::read('limit');
        $paginate['order'] = array('_id' => 'desc'); 
        
        $this->paginate = $paginate;
        $data = $this->paginate('Shortern');
        $this->set('data',$data);

        $this->set('domains',$this->Domain->find('list'));
        $this->set('redirect',array('direct'=>'direct','frame'=>'frame','splash'=>'splash'));
    }

    public function shorternopr() {
		$this->set('title','Shortern');
        $this->set('subtitle','urls');
        
        if($this->request->data){
            $this->Shortern->set($this->request->data);
            if ($this->Shortern->validates() == true) {
                
                $data = $this->request->data['Shortern'];
                if(isset($data['_id']) && $data['_id'] != ''){
                    //update data
                    if($this->Shortern->save($data)){
                        $this->Session->setFlash(__('Record successfully saved.'),'success');
                        $this->redirect(array('controller'=>'shortern','action'=>'index'));
                        exit;
                    }
                }else{
                    //Insert new urls
                    //check url already exists
                    $params = array('fields' => array('url'),'conditions' => array('url' => $data['url']));
                    $resultSet = $this->Shortern->find('first',$params);
                    if($resultSet){
                        $this->Session->setFlash(__('Url already short.'),'error');
                    }else{
                        $data['alias'] = isset($data['alias']) && $data['alias'] != '' ? $data['alias'] : ''; 
                        $data['password'] = isset($data['password']) && $data['password'] != '' ? $data['password'] : ''; 
                        $data['key'] = $this->genkey($data['url']);
                        $data['uid'] = 0;
                        if($this->Shortern->save($data)){
                            $this->Session->setFlash(__('Record successfully saved.'),'success');
                            $this->redirect(array('controller'=>'shortern','action'=>'index'));
                            exit;
                        }
                    }
                }
            }
        }

        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            switch($opr['opr']){
                case 'edit' :
                    $this->request->data = $this->Shortern->find('first',array('conditions'=>array('_id'=>$opr['id'])));
                    break;
                case 'delete' :
                    if($this->Shortern->delete(array(new MongoId($opr['id'])))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'shortern','action'=>'index'));
                    exit;
                    break;
            }
        }

        $this->set('domains',$this->Domain->find('list'));
        $this->set('redirect',array('direct'=>'direct','frame'=>'frame','splash'=>'splash'));
	}

    public function domains() {
    	$this->set('title','Shortern');
        $this->set('subtitle','Domains');

        $condition = $paginate = array();

        if(isset($_GET['s'])){
            $this->request->data['search'] = _decode($_GET['s']);
        }
        
        if($this->request->data){

        	if(isset($this->request->data['search']['domain']) && $this->request->data['search']['domain'] != ''){
                $condition['Domain.domain like'] = '%'.$this->request->data['search']['domain'].'%';
            }
            
            if(isset($this->request->data['search']['status'])){
                $condition['Domain.status'] = $this->request->data['search']['status'];
            }
        }
        
        $paginate['conditions'] = $condition;
        $paginate['fields'] = array('Domain.*');
        $paginate['limit'] = Configure::read('limit');
        $paginate['order'] = array('id' => 'desc'); 
        
        $this->paginate = $paginate;
        $data = $this->paginate('Domain');
        $this->set('data',$data);
    }

    public function domainopr() {
    	$this->set('title','Shortern');
        $this->set('subtitle','Domains');

        if($this->request->data){
            $this->Domain->set($this->request->data);
            if ($this->Domain->validates()) {
                $data =  $this->Domain->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect(array('controller'=>'shortern','action'=>'domains'));
            } else {
                $errors = $this->Domain->validationErrors;
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            switch($opr['opr']){
                case 'edit' :
                    $this->request->data = $this->Domain->find('first',array('conditions'=>array('id'=>$opr['id'])));
                    break;
                case 'delete' :
                    if($this->Domain->delete(array('id'=>$opr['id']))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'shortern','action'=>'domains'));
                    exit;
                    break;
            }
        }
    }
}