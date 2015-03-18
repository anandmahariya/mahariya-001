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
        
        $sites = array_merge(array(''=>'All','0'=>'Direct'),$this->Site->find('list'));
        $this->set('sites_array',$sites);
        
    }
    
    public function renderchart($type = null) {
        $result = array();
        switch($type){
            case 'analytic_city' :
                $post = $_POST['data']['analytics'];
                $date_s = $this->Common->mysqlDate($post['date'],'dd/mm/yy','start');
                $date_e = $this->Common->mysqlDate($post['date'],'dd/mm/yy','end');
                
                $conditions = '';
                if($post['site'] != ''){
                    $conditions = sprintf('And r.site_id = %d',$post['site']);
                }
                
                $query = sprintf('SELECT SUM(1) as `tot`,ips.country,ips.country_code,ips.state,ips.city FROM `requests` r
                                    left join ips on ips.ip_long = r.ip_long 
                                    where  r.created between "%s" and "%s" %s
                                    group by ips.city order by tot desc',$date_s,$date_e,$conditions);
                
                $dataset = $this->Request->query($query);
                $total = 0;
                $table = '<table class="table table-striped"><tbody><tr><th>Country</th><th>State</th><th>City</th><th>Visitors</th></tr>';
                foreach($dataset as $key=>$val){
                    $tmp = isset($val[0]['tot']) ? $val[0]['tot'] : 0;
                    $total += $tmp;
                    $table .= sprintf('<tr>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%d</td>
                                        </tr>',$val['ips']['country']!=''?$val['ips']['country']:'--',
                                        $val['ips']['state']!=''?$val['ips']['state']:'--',
                                        $val['ips']['city']!=''?$val['ips']['city']:'--'
                                        ,$tmp);
                }
                $table .= sprintf('<tr><td></td><td></td><td><b>Total</b></td><td>%d</td></tr>',$total);
                echo $table .= '</table>';
                exit;
                break;
            case 'analytic_request' :
                $post = $_POST['data']['analytics'];
                $date = strtotime($this->Common->mysqlDate($post['date'],'dd/mm/yy'));
                $fields = array();
                for($i=0;$i<24;$i++){
                    $fields[] = sprintf("SUM(if(requests.created between '%s' AND '%s',1,0)) as '%s' ",date('Y-m-d G:i:s',mktime($i,0,0,date('m',$date),date('d',$date),date('Y',$date))),date('Y-m-d G:i:s',mktime($i,59,59,date('m',$date),date('d',$date),date('Y',$date))),$i.':00 - '.$i.':59');
                }
                
                if($post['site'] != ''){
                    $query = sprintf('select %s,sites.name from requests left join sites on sites.id = requests.site_id where requests.site_id = %d group by requests.site_id ', implode(',',$fields),$post['site']);
                }else{
                    $query = sprintf('select %s,sites.name from requests left join sites on sites.id = requests.site_id group by requests.site_id  ', implode(',',$fields));
                }
                
                $dataset = $this->Request->query($query);
                
                $tmp = array();
                foreach($dataset as $key=>$val){
                    $site = isset($val['sites']['name']) && $val['sites']['name']!='' ? $val['sites']['name'] : 'Direct';
                    $tmp[$site] = $val[0];
                }
                
                $result['color'] = $this->Common->randColor(count($tmp));
                $result['labels'] = array_keys($tmp);
                foreach($tmp as $key=>$val){
                    foreach($val as $k=>$v){
                        $result['data'][$k]['y'] = $k;
                        $result['data'][$k][$key] = $v;
                    }
                }
                $result['data'] = array_values($result['data']);
                break;
            case 'analytic_request_vip' : //valid invalid proxy
                $post = $_POST['data']['analytics'];
                $date_s = $this->Common->mysqlDate($post['date'],'dd/mm/yy','start');
                $date_e = $this->Common->mysqlDate($post['date'],'dd/mm/yy','end');
                
                $conditions = '';
                $sites = array();
                if($post['site'] != ''){
                    $conditions .= ' and r.site_id = '.$post['site'];
                }else{
                    $sites = array('Direct'=>array('y'=>'Direct','total'=>0,'valid'=>0,'in-valid'=>0,'proxy'=>0));
                    $tmp = $this->Site->find('list');
                    foreach($tmp as $key=>$val){
                        $sites[$val] = array('y'=>$val,'total'=>0,'valid'=>0,'in-valid'=>0,'proxy'=>0);
                    }
                }
                
                
                $query = sprintf('SELECT 
                                    s.name,
                                    SUM(1) as `total`,
                                    SUM(if(r.valid = 1,1,0)) as `valid`,
                                    SUM(if(r.valid = 0 and r.proxy = 0,1,0)) as `in-valid`,
                                    SUM(if(r.valid = 0 and r.proxy = 1,1,0)) as `proxy`
                                    FROM `requests` r
                                    left join sites s on s.id = r.site_id
                                    where r.created between "%s" and "%s" %s group by r.site_id order by r.site_id',$date_s,$date_e,$conditions);
                
                $dataset = $this->Request->query($query);
                foreach($dataset as $key=>$val){
                    $tmp = isset($val['s']['name']) && $val['s']['name']!='' ? $val['s']['name'] : 'Direct';
                    $sites[$tmp] = $val[0];
                    $sites[$tmp]['y'] = $tmp;
                }
                
                $result['color'] = array('green','blue','red','brown');//$this->Common->randColor(count($sites));
                $result['labels'] = array('total','valid','in-valid','proxy');
                $result['data'] = array_values($sites);
                break;
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
                    $query = sprintf('SELECT r.ip,SUM(1) as `tot` FROM `requests` r where r.created between "%s 00:00:00" AND "%s 23:59:59"  group by r.ip order by `tot` desc limit 0,10',date('Y-m-d'),date('Y-m-d'));
                    $dataset = $this->Request->query($query);
                    $result['color'] = $this->Common->randColor(count($dataset));
                    foreach($dataset as $key=>$val){
                        $result['data'][] = array('label'=>$val['r']['ip'],'value'=>$val['0']['tot']);   
                    }
                break;
            case 'clickdata' :
                    $query = sprintf('SELECT SUM(1) as `total`,SUM(if(r.valid = 1,1,0)) as `valid`,SUM(if(r.valid = 0,1,0)) as `invalid` FROM `requests` r where r.created between "%s 00:00:00" AND "%s 23:59:59" ',date('Y-m-d'),date('Y-m-d'));
                    $tmp = $this->Request->query($query);
                    $requests = array('total'=>0,'valid'=>0,'invalid'=>0);
                    if(isset($tmp[0][0])){
                        $requests = array_merge($requests,$tmp[0][0]);
                    }
                    $result = $requests;
                break;
            case 'statechart' :
                $query = sprintf('SELECT i.state,sum(1) as tot FROM `requests` r
                                 left join ips i on i.ip = r.ip
                                 where
                                 i.country_code = "US"
                                 AND i.state != ""
                                 AND r.created between "%s 00:00:00" AND "%s 23:59:59"
                                 group by i.state order by tot desc',date('Y-m-d'),date('Y-m-d'));
                
                $dataset = $this->Request->query($query);
                $result['color'] = $this->Common->randColor(count($dataset));
                foreach($dataset as $key=>$val){
                    $result['data'][] = array('label'=>ucwords($val['i']['state']),'value'=>$val['0']['tot']);   
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
                                    'Request.referer',
                                    'Request.site_referer',
                                    'SUM(1) as hits',
                                    'Request.valid',
                                    'Request.proxy',
                                    'Request.comments',
                                    'ip.dns',
                                    'Request.mobile',
                                    'Request.created',
                                    'ip.country',
                                    'ip.country_code',
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
    
    public function analytics() {
        
        $this->set('sites_array',$this->Site->find('list'));
    }
}