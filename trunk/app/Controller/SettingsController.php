<?php
class SettingsController extends AppController {
    
    public $uses = array('Option');
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
}