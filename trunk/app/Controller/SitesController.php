<?php
class SitesController extends AppController {

    public $uses = array('Site','Replacer','ValidZone','RestrictedZone','AdminZone','Country','State','City','Request');
    var $helpers = array('Html');
    
    public function beforefilter(){
        $this->set('title','Sites');
        $this->set('subtitle','Control panel');
    }
    
    public function index() {
        $condition = $paginate = array();
        
        if(isset($_GET['s'])){
            $this->request->data['search'] = _decode($_GET['s']);
        }
        
        if($this->request->data){
            if(isset($this->request->data['search']['name']) && $this->request->data['search']['name'] != ''){
                $condition['Site.name like'] = '%'.$this->request->data['search']['name'].'%';
            }
            
            if(isset($this->request->data['search']['status'])){
                $condition['Site.status'] = $this->request->data['search']['status'];
            }
        }
        
        $paginate['conditions'] = $condition;
        $paginate['fields'] = array('Site.*');
        $paginate['limit'] = Configure::read('limit');
        $paginate['order'] = array('id' => 'desc'); 
        
        $this->paginate = $paginate;
        $data = $this->paginate('Site');
        $this->set('data',$data);
    }
    
    public function siteopr() {
        
        if($this->request->data){
            $this->Site->set($this->request->data);
            if ($this->Site->validates()) {
                $tmp = parse_url($this->request->data['Site']['name']);
                $this->request->data['Site']['name'] = $tmp['scheme'].'://'.$tmp['host'];
                $data =  $this->Site->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect(array('controller'=>'sites','action'=>'index'));
            } else {
                $errors = $this->Site->validationErrors;
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            switch($opr['opr']){
                case 'edit' :
                    $this->request->data = $this->Site->find('first',array('conditions'=>array('id'=>$opr['id'])));
                    break;
                case 'delete' :
                    if($this->Site->delete(array('id'=>$opr['id']))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'sites','action'=>'index'));
                    exit;
                    break;
                case 'reset' :
                    if($this->Request->deleteAll(array('Request.site_id'=>$opr['id']))){
                        $this->Session->setFlash(__('Record successfully deleted.'),'success');
                    }else{
                        $this->Session->setFlash(__('Record not deleted.'),'error');
                    }
                    $this->redirect(array('controller'=>'sites','action'=>'index'));
                    exit;
                    break;
            }
        }
    }
    
    public function replacearea() {
        
        $tmp = _decode($_GET['action']);
        $back = array('controller'=>'sites','action'=>'index');
        
        $this->set('back',$back);
        $this->set('client',$this->Replacer->find('all',array('conditions'=>array('site_id'=>$tmp['id'],'owner'=>0))));
        $this->set('owner',$this->Replacer->find('all',array('conditions'=>array('site_id'=>$tmp['id'],'owner'=>1))));
        $this->set('site_id',$tmp['id']);
    }
    
    public function replaceropr() {
        
        $tmp = _decode($_GET['action']);
        $back = array('controller'=>'sites',
                      'action'=>'replacearea',
                      '?'=>array('action'=>_encode(array('id'=>$tmp['site_id']))));
        
        $formUrl = array('controller'=>'sites',
                        'action'=>'replaceropr',
                        '?'=>array('action'=>_encode(array('site_id'=>$tmp['site_id'],'owner'=>$tmp['owner']))));
        
        $this->set('back',$back);
        $this->set('formUrl',$formUrl);
        $this->set('site_id',$tmp['site_id']);
        $this->set('owner',$tmp['owner']);
        
        if($this->request->data){
            $this->Replacer->set($this->request->data);
            if ($this->Replacer->validates()) {
                $data =  $this->Replacer->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect($back);
            } else {
                $errors = $this->Site->validationErrors;
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            if(isset($opr['opr'])){
                switch($opr['opr']){
                    case 'edit' :
                        $this->request->data = $this->Replacer->find('first',array('conditions'=>array('id'=>$opr['id'])));
                        $this->request->data['Replacer']['script_type'] = $this->request->data['Replacer']['name'];
                        break;
                    case 'delete' :
                        if($this->Replacer->delete(array('id'=>$opr['id']))){
                            $this->Session->setFlash(__('Record successfully deleted.'),'success');
                        }else{
                            $this->Session->setFlash(__('Record not deleted.'),'error');
                        }
                        $this->redirect(array('controller'=>'sites','action'=>'replacearea','?'=>array('action'=>_encode(array('id'=>$tmp['site_id'])))));
                        exit;
                        break;
                }
            }
        }
        $this->set('type',array('id'=>'Id','class'=>'Class','script'=>'Script'));
        $this->set('script_type',array('redirect'=>'Redirect','server_redirect'=>'Server Redirect'));
        
    }
    
    public function adminzone() {
        
        $qs = _decode($_GET['action']);
        $back = array('controller'=>'sites','action'=>'index');
        
        $query = sprintf("select az.id,c.name as country,
                         if(s.name is null,'*',s.name) as state,
                         if(ci.city is null,'*',ci.city) as city,
                         az.status from admin_zones az
                         left join countries c on c.code = az.country
                         left join states s on s.country_code = az.country AND s.code = az.state
                         left join cities ci on ci.id = az.city where az.site_id = %d ",$qs['id']);
        
        $tmp = $this->AdminZone->query($query);
        
        $response = array();
        foreach($tmp as $key=>$val){
            $response[] = array('AdminZone'=>array('id'=>$val['az']['id'],'country'=>$val['c']['country'],'state'=>$val['0']['state'],'city'=>$val['0']['city'],'status'=>$val['az']['status']));
        }
        
        $this->set('site_id',$qs['id']);
        $this->set('back',$back);
        $this->set('data',$response);
        $this->set('title','Admin Zone');
        $this->set('subtitle','Control panel');
    }
    
    public function adminzoneopr() {
        $tmp = _decode($_GET['action']);
        $back = array('controller'=>'sites',
                      'action'=>'adminzone',
                      '?'=>array('action'=>_encode(array('id'=>$tmp['site_id']))));
        
        $formUrl = array('controller'=>'sites',
                        'action'=>'adminzoneopr',
                        '?'=>array('action'=>_encode(array('site_id'=>$tmp['site_id']))));
        
        $this->set('back',$back);
        $this->set('formUrl',$formUrl);
        $this->set('site_id',$tmp['site_id']);
        
        if($this->request->data){
            $this->AdminZone->set($this->request->data);
            if ($this->AdminZone->validates()) {
                $data =  $this->AdminZone->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect($back);
            } else {
                $errors = $this->Site->validationErrors;
            }
        }
        
        
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            if(isset($opr['opr'])){
                switch($opr['opr']){
                    case 'delete' :
                        if($this->AdminZone->delete(array('id'=>$opr['id']))){
                            $this->Session->setFlash(__('Record successfully deleted.'),'success');
                        }else{
                            $this->Session->setFlash(__('Record not deleted.'),'error');
                        }
                        $this->redirect(array('controller'=>'sites','action'=>'adminzone','?'=>array('action'=>_encode(array('id'=>$tmp['site_id'])))));
                        exit;
                        break;
                }
            }
        }
        
        //Valid country list
        $country = array();
        $query = sprintf('SELECT c.code,c.name FROM `countries` c left join valid_zones vz on vz.country = c.code where vz.status = 1');
        $tmp = $this->Country->query($query);
        foreach($tmp as $key=>$val){
            if(isset($val['c']['code']) && isset($val['c']['name'])){
                $country[$val['c']['code']] = $val['c']['name'];
            }
        }
        $this->set('country',$country);
    }
    
    public function validzone() {
        
        $query = sprintf("select rz.id,c.name as country,
                         if(s.name is null,'*',s.name) as state,
                         if(ci.city is null,'*',ci.city) as city,
                         rz.status from valid_zones rz
                         left join countries c on c.code = rz.country
                         left join states s on s.country_code = rz.country AND s.code = rz.state
                         left join cities ci on ci.id = rz.city");
        
        $tmp = $this->ValidZone->query($query);
        
        $response = array();
        foreach($tmp as $key=>$val){
            $response[] = array('ValidZone'=>array('id'=>$val['rz']['id'],'country'=>$val['c']['country'],'state'=>$val['0']['state'],'city'=>$val['0']['city'],'status'=>$val['rz']['status']));
        }
        
        $this->set('data',$response);
        $this->set('title','Valid Zone');
        $this->set('subtitle','Control panel');
    }
    
    public function validzoneopr(){
        
        $back = array('controller'=>'sites','action'=>'validzone');
        $formUrl = array('controller'=>'sites','action'=>'validzoneopr');
        
        if($this->request->data){
            $this->ValidZone->set($this->request->data);
            if ($this->ValidZone->validates()) {
                $data =  $this->ValidZone->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect($back);
            } else {
                $errors = $this->Site->validationErrors;
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            if(isset($opr['opr'])){
                switch($opr['opr']){
                    case 'delete' :
                        if($this->ValidZone->delete(array('id'=>$opr['id']))){
                            $this->Session->setFlash(__('Record successfully deleted.'),'success');
                        }else{
                            $this->Session->setFlash(__('Record not deleted.'),'error');
                        }
                        $this->redirect(array('controller'=>'sites','action'=>'validzone'));
                        exit;
                        break;
                }
            }
        }
        
        $this->set('country',$this->Country->find('list',array('fields'=>array('Country.code','Country.name'),'order'=>array('Country.name'))));
        
        $this->set('back',$back);
        $this->set('formUrl',$formUrl);
        
        $this->set('title','Valid Zone');
        $this->set('subtitle','Control panel');
    }
    
    public function restrictedzone() {
        
        $query = sprintf("select rz.id,c.name as country,
                         if(s.name is null,'*',s.name) as state,
                         if(ci.city is null,'*',ci.city) as city,
                         rz.status from restricted_zones rz
                         left join countries c on c.code = rz.country
                         left join states s on s.country_code = rz.country AND s.code = rz.state
                         left join cities ci on ci.id = rz.city");
        
        $tmp = $this->RestrictedZone->query($query);
        
        $response = array();
        foreach($tmp as $key=>$val){
            $response[] = array('RestrictedZone'=>array('id'=>$val['rz']['id'],'country'=>$val['c']['country'],'state'=>$val['0']['state'],'city'=>$val['0']['city'],'status'=>$val['rz']['status']));
        }
        
        $this->set('data',$response);
        $this->set('title','Restricted Zone');
    }
    
    public function restrictedzoneopr(){
        
        $back = array('controller'=>'sites','action'=>'restrictedzone');
        $formUrl = array('controller'=>'sites','action'=>'restrictedzoneopr');
        
        if($this->request->data){
            $this->RestrictedZone->set($this->request->data);
            if ($this->RestrictedZone->validates()) {
                $data =  $this->RestrictedZone->save($this->request->data);
                $this->Session->setFlash(__('Record successfully saved.'),'success');
                $this->redirect($back);
            } else {
                $errors = $this->RestrictedZone->validationErrors;
            }
        }
        
        if(isset($_GET['action'])){
            $opr = _decode($_GET['action']);
            if(isset($opr['opr'])){
                switch($opr['opr']){
                    case 'delete' :
                        if($this->RestrictedZone->delete(array('id'=>$opr['id']))){
                            $this->Session->setFlash(__('Record successfully deleted.'),'success');
                        }else{
                            $this->Session->setFlash(__('Record not deleted.'),'error');
                        }
                        $this->redirect(array('controller'=>'sites','action'=>'restrictedzone'));
                        exit;
                        break;
                }
            }
        }
        
        $this->set('country',$this->Country->find('list',array('fields'=>array('Country.code','Country.name'),'order'=>array('Country.name'))));
        
        $this->set('back',$back);
        $this->set('formUrl',$formUrl);
        
        $this->set('title','Restricted Zone');
    }
    
    public function setstatus($type,$id = null,$value = 0){
        $data = array('id'=>$id,'status'=>$value == 0 ? 1 : 0);
        $response = array();
        switch($type){
            case 'validzone' :
                if($data['id'] != null){
                    if($tmp = $this->ValidZone->save($data)){
                        $response['status'] = true;
                        $response['value'] = $tmp['ValidZone']['status'];
                    }else{
                        $response['status'] = false;    
                    }
                }else{
                    $response['status'] = false;
                }                
                break;
            case 'restrictedzone' :
                if($data['id'] != null){
                    if($tmp = $this->RestrictedZone->save($data)){
                        $response['status'] = true;
                        $response['value'] = $tmp['RestrictedZone']['status'];
                    }else{
                        $response['status'] = false;    
                    }
                }else{
                    $response['status'] = false;
                }                
                break;
            case 'validsite' :
                if($data['id'] != null){
                    if($tmp = $this->Site->save($data)){
                        $response['status'] = true;
                        $response['value'] = $tmp['Site']['status'];
                    }else{
                        $response['status'] = false;    
                    }
                }else{
                    $response['status'] = false;
                }                
                break;
            case 'replacer' :
                if($data['id'] != null){
                    if($tmp = $this->Replacer->save($data)){
                        $response['status'] = true;
                        $response['value'] = $tmp['Replacer']['status'];
                    }else{
                        $response['status'] = false;    
                    }
                }else{
                    $response['status'] = false;
                }                
                break;
        }
        echo json_encode($response);
        exit;
    }
    
    
    public function autocomplete($type,$value,$value2 = null){
        $response = array();
        $this->layout = 'ajax';
        switch($type){
            case 'state' :
                $tmp = $this->State->find('all',array('fields'=>array('State.id','State.code','State.name'),
                                                            'conditions'=>array('country_code'=>$value),
                                                            'order'=>array('State.name')));
                foreach($tmp as $key=>$val){
                    $response[] = $val['State'];
                }
                break;
            case 'city' :
                $tmp = $this->City->find('all',array('fields'=>array('City.id','City.city'),
                                                            'conditions'=>array('country_code'=>$value,'region_code'=>$value2),
                                                            'order'=>array('City.city')));
                foreach($tmp as $key=>$val){
                    $response[] = $val['City'];
                }
                break;
        }
        echo json_encode($response);
        exit;
    }
}