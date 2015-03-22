<?php
class SettingsController extends AppController {
    
    public $components = array('RequestHandler','Common');
    public $uses = array('Option','Blockip','Blockas');
    public function beforefilter(){
        $this->set('title','Settings');
        $this->set('subtitle','Control panel');
    }
    
    public function index() {

    }
    
    public function getscript() {
        
    }
    
    public function conditions() {
        if($this->request->data){
            $this->Option->set($this->request->data);
            if ($this->Option->validates()) {
                //Check key is exists or not
                $data = $this->Option->find('first',array('conditions'=>array('key'=>'conditions')));
                if($data){
                    $data['Option']['value'] = serialize($this->request->data['options']);
                    if($this->Option->save($data)){
                        $this->Session->setFlash(__('Record successfully saved.'),'success');
                    }
                }else{
                    $data = array('key'=>'conditions','value'=>serialize($this->request->data['options']));
                    if($this->Option->save($data)){
                        $this->Session->setFlash(__('Record successfully saved.'),'success');
                    }
                }
            }
        }
        
        //Fill default value
        $data = $this->Option->find('first',array('conditions'=>array('key'=>'conditions')));
        if($data){
            $this->request->data['options'] = unserialize($data['Option']['value']);    
        }
    }
    
    public function blockas() {
        $condition = $paginate = array();
        
        if($this->request->data){
            if(isset($this->request->data['search']['as']) && $this->request->data['search']['as'] != ''){
                $condition[] = array('Blockas.as'=>$this->request->data['search']['as']);
            }
        }
        
        $paginate['conditions'] = $condition;
        $paginate['fields'] = array('Blockas.*');
        $paginate['limit'] = Configure::read('limit');
        $paginate['order'] = array('id' => 'desc'); 
        
        $this->paginate = $paginate;
        $data = $this->paginate('Blockas');
        $this->set('data',$data);
    }
    
    public function blockasopr() {
        if($this->request->data){
            $this->Blockas->set($this->request->data);
            if ($this->Blockas->validates()) {
                if($this->Blockas->save($this->request->data)){
                    $this->Session->setFlash(__('Record successfully saved.'),'success');
                    $this->redirect(array('controller'=>'settings','action'=>'blockas'));
                }
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            switch($opr['opr']){
                case 'delete' :
                    if($this->Blockas->delete(array('id'=>$opr['id']))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'settings','action'=>'blockas'));
                    exit;
                    break;
            }
        }
    }
    
    public function blockip() {
        $condition = $paginate = array();
        
        if($this->request->data){
            if(isset($this->request->data['search']['ip']) && $this->request->data['search']['ip'] != ''){
                $condition[] = array('INET_ATON("'.$this->request->data['search']['ip'].'") BETWEEN Blockip.start AND Blockip.end');
            }
        }
        
        $paginate['conditions'] = $condition;
        $paginate['fields'] = array('Blockip.*');
        $paginate['limit'] = Configure::read('limit');
        $paginate['order'] = array('id' => 'desc'); 
        
        $this->paginate = $paginate;
        $data = $this->paginate('Blockip');
        $this->set('data',$data);
    }
    
    public function blockipopr() {
        if($this->request->data){
            $this->Blockip->set($this->request->data);
            if ($this->Blockip->validates()) {
                $this->request->data['Blockip']['start'] = ip2long($this->request->data['Blockip']['start']);
                $this->request->data['Blockip']['end'] = ip2long($this->request->data['Blockip']['end']);
                if($this->Blockip->save($this->request->data)){
                    $this->Session->setFlash(__('Record successfully saved.'),'success');
                    $this->redirect(array('controller'=>'settings','action'=>'blockip'));
                }
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            switch($opr['opr']){
                case 'delete' :
                    if($this->Blockip->delete(array('id'=>$opr['id']))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'settings','action'=>'blockip'));
                    exit;
                    break;
            }
        }
    }

}