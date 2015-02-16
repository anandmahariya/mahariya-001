<?php
class DashboardController extends AppController {
    
    public $components = array('RequestHandler','Common');
    public $uses = array('Site','Replacer','ValidZone','AdminZone','Country','State','City','Ip','Request');
    
    public function beforefilter(){
        $this->set('title','Dashboard');
        $this->set('subtitle','Control panel');
    }
    
    public function index() {
        
        //Total sites enable and disable
        $query = sprintf('SELECT SUM(1) as `total`,SUM(if(sites.status = 1,1,0)) as `enable`,SUM(if(sites.status = 0,1,0)) as `disable` FROM `sites` ');
        $tmp = $this->Site->query($query);
        $sites = array('total'=>0,'enable'=>0,'disable'=>0);
        if(isset($tmp[0][0])){
            $sites = array_merge($sites,$tmp[0][0]);
        }
        $this->set('sites',$sites);
        
        //Total request valid and Invalid
        $query = sprintf('SELECT SUM(1) as `total`,SUM(if(requests.valid = 1,1,0)) as `valid`,SUM(if(requests.valid = 0,1,0)) as `invalid` FROM `requests` ');
        $tmp = $this->Request->query($query);
        $requests = array('total'=>0,'valid'=>0,'invalid'=>0);
        if(isset($tmp[0][0])){
            $requests = array_merge($requests,$tmp[0][0]);
        }
        $this->set('requests',$requests);
        
        $sites = array_merge(array(''=>'All','0'=>'Direct'),$this->Site->find('list'));
        $this->set('sites_array',$sites);
        
    }
    
    public function renderchart($type = null) {
        $result = array();
        switch($type){
            case 'request' :
                    $post = $_POST['data']['request'];
                    $totalDays = date('t',mktime(0,0,0,$post['month'],1,$post['year']));
                    $fields = array();
                    for($i=1;$i<=$totalDays;$i++){
                        $fields[] = sprintf("SUM(if(requests.created between '%s' AND '%s',1,0)) as '%s' ",date('Y-m-d G:i:s',mktime(0,0,0,$post['month'],$i,$post['year'])),date('Y-m-d G:i:s',mktime(23,59,59,$post['month'],$i,$post['year'])),date('M d',mktime(23,59,59,$post['month'],$i,$post['year'])));
                    }
                    
                    if($post['site'] != ''){
                        $query = sprintf('select %s,requests.valid from requests where requests.site_id = %d group by requests.valid', implode(',',$fields),$post['site']);
                    }else{
                        $query = sprintf('select %s,requests.valid from requests group by requests.valid', implode(',',$fields));
                    }
                    
                    $dataset = $this->Request->query($query);
                    
                    $tmp = array();
                    foreach($dataset as $key=>$val){
                        $line = isset($val['requests']['valid']) && $val['requests']['valid'] > 0 ? 'valid' : 'invalid';
                        unset($val['requests']);
                        foreach($val as $k=>$v){
                            foreach($v as $k1=>$v1){
                                $tmp[$k1][$line] = $v1;       
                            }   
                        }
                    }
                    
                    $result['color'] = array('blue','red','green');
                    foreach($tmp as $key=>$val){
                        $val['valid'] = isset($val['valid']) ? $val['valid'] : 0;
                        $val['invalid'] = isset($val['invalid']) ? $val['invalid'] : 0;
                        $result['data'][] = array('y'=>$key,'valid'=>$val['valid'],'invalid'=>$val['invalid'],'total'=>$val['valid'] + $val['invalid']);
                    }
                break;
            case 'topip' :
                    $query = sprintf('SELECT r.ip,SUM(1) as `tot` FROM `requests` r where r.site_id = %d AND r.created between "%s 00:00:00" AND "%s 23:59:59"  group by r.ip order by `tot` desc limit 0,10',0,'2015-02-13','2015-02-13');
                    $dataset = $this->Request->query($query);
                    $result['color'] = $this->Common->randColor(count($dataset));
                    foreach($dataset as $key=>$val){
                        $result['data'][] = array('label'=>$val['r']['ip'],'value'=>$val['0']['tot']);   
                    }
                break;
        }
        echo json_encode($result);
        exit;
    }
    
    public function search() {
        $this->set('subtitle','Search');
        if(isset($_GET['s'])){
            $this->request->data['search'] = _decode($_GET['s']);
        }
        
        $condition = $state = $city = array();
        if($this->request->data){
            
            $sdata = array_merge(array('ip'=>'','site'=>'','country'=>'','state'=>'','city'=>'','startdate'=>'','enddate'=>'','valid'=>''),$this->request->data['search']);
            if($sdata['ip'] != ''){
                $condition['Request.ip'] = $sdata['ip'];
            }
            
            if($sdata['site'] != ''){
                $condition['Request.site_id'] = $sdata['site'];
            }
            
            if($sdata['country'] != ''){
                $condition['ip.country_code'] = $sdata['country'];
                $tmp = $this->State->find('all',array('fields'=>array('State.id','State.code','State.name'),
                                                            'conditions'=>array('country_code'=>$sdata['country']),
                                                            'order'=>array('State.name')));
                
                foreach($tmp as $key=>$val){
                    $state[$val['State']['code']] = $val['State']['name'];
                }
            }
            
            if($sdata['state'] != '' && $sdata['state'] != '*'){
                $condition['ip.state'] = $state[$sdata['state']];
                $tmp = $this->City->find('all',array('fields'=>array('City.id','City.city'),
                                                            'conditions'=>array('country_code'=>$sdata['country'],'region_code'=>$sdata['state']),
                                                            'order'=>array('City.city')));
                foreach($tmp as $key=>$val){
                    $city[$val['City']['id']] = $val['City']['city'];
                }
            }
            
            if($sdata['city'] != '' && $sdata['city'] != '*'){
                $condition['ip.city'] = $city[$sdata['city']];
            }
            
            if($sdata['startdate'] != '' && $sdata['enddate'] != ''){
                $tsdate = $this->Common->mysqlDate($sdata['startdate'],'dd/mm/yy','start');
                $tedate = $this->Common->mysqlDate($sdata['enddate'],'dd/mm/yy','end');
                $condition['Request.created >='] = $tsdate;
                $condition['Request.created <='] = $tedate;
            }elseif($sdata['startdate'] != ''){
                $tsdate = $this->Common->mysqlDate($sdata['startdate'],'dd/mm/yy','start');
                $tedate = $this->Common->mysqlDate($sdata['startdate'],'dd/mm/yy','end');
                $condition['Request.created >='] = $tsdate;
                $condition['Request.created <='] = $tedate;
            }elseif($sdata['enddate'] != ''){
                $tsdate = $this->Common->mysqlDate($sdata['enddate'],'dd/mm/yy','start');
                $tedate = $this->Common->mysqlDate($sdata['enddate'],'dd/mm/yy','end');
                $condition['Request.created >='] = $tsdate;
                $condition['Request.created <='] = $tedate;
            }
            
            //echo '<pre>';print_r($condition);echo '</pre>';exit;
        }
        
        $paginate = array();
        $paginate['conditions'] = $condition;
        $paginate['joins'] = array(array('alias' => 'ip','table' => 'ips','type' => 'LEFT','conditions' => array('Request.ip = ip.ip')),
                                   array('alias' => 's','table' => 'sites','type' => 'LEFT','conditions' => array('Request.site_id = s.id')));
        
        $paginate['fields'] = array('Request.ip',
                                    'if(s.name is null,"Direct",s.name) as site',
                                    'SUM(1) as hits',
                                    'Request.valid',
                                    'Request.proxy',
                                    'Request.created',
                                    'ip.country',
                                    'ip.state',
                                    'ip.city');
        
        $paginate['limit'] = Configure::read('limit');
        $paginate['group'] = array('Request.ip','Request.created');
        $paginate['order'] = array('Request.created' => 'desc');
        
        $this->paginate = $paginate;
        $data = $this->paginate('Request');
        $this->set('data', $data);
        
        $this->set('country',$this->Country->find('list',array('fields'=>array('Country.code','Country.name'),'order'=>array('Country.name'))));
        $this->set('state',$state);
        $this->set('city',$city);
        
        $sites = array_merge(array(''=>'All','0'=>'Direct'),$this->Site->find('list'));
        $this->set('sites_array',$sites);
        
    }
}