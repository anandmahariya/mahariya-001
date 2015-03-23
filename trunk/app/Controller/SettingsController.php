<?php
class SettingsController extends AppController {
    
    public $components = array('RequestHandler','Common');
    public $uses = array('Option','Blockip','Blockas','BlockasDomain');
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
        $paginate['joins'] = array(array('alias' => 'bad','table' => 'blockas_domains','type' => 'LEFT','conditions' => array('Blockas.as = bad.as')));
        $paginate['fields'] = array('Blockas.*,SUM(1) as `tot`');
        $paginate['group'] = array('Blockas.as');
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
                
                App::import('Controller', 'getscript');
                $getscript = new GetscriptController;
                preg_match_all('/http+[a-z|\/|.|:|0-9|-]+/i',$this->request->data['Blockas']['domains'],$urls);
                $msg = '';
                foreach($urls[0] as $key=>$val){
                    $parseURL = parse_url($val);
                    if(isset($parseURL['host']) && $parseURL['host'] != ''){
                        $recordSet = $this->BlockasDomain->find('all',array('conditions'=>array('name'=>$parseURL['host'])));
                        if($recordSet){
                            $msg .= sprintf('<li>%s URL already in database.</li>',$parseURL['host']);
                        }else{
                            $ip = gethostbyname($parseURL['host']);
                            if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
                                $data = $getscript->get_originAS($ip);
                                if(isset($data['as']) && $data['as'] != '' && isset($data['as_name']) && $data['as_name'] != ''){
                                    $blockas = array('id'=>null,'as'=>$data['as'],'name'=>$data['as_name']);
                                    $blockas_domains = array('id'=>null,'as'=>$data['as'],'name'=>$parseURL['host']);
                                    
                                    if($this->BlockasDomain->save($blockas_domains)){
                                        $msg .= sprintf('<li>%s successfully saved.</li>',$parseURL['host']);
                                    }
                                    
                                    //Save data in blockas
                                    $tmp = $this->Blockas->find('all',array('conditions'=>array('as'=>$blockas['as'])));
                                    if(!$tmp){
                                        if($this->Blockas->save($blockas)){
                                            $msg .= sprintf('<li>AS%d Origin successfully saved.</li>',$blockas['as']);
                                        }
                                    }
                                }
                            }else{
                                $msg .= sprintf('<li class="text-red">%s host IP not found.</li>',$parseURL['host']);
                            }
                        }
                    }else{
                        $msg .= sprintf('<li class="text-red">%s not valid URL.</li>',$val);
                    }
                }
                $this->Session->setFlash(sprintf('<ul>%s</ul>',$msg),'success');
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