<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class AnalyticsController extends AppController {
	
	public $components = array('RequestHandler','Common');
	public $uses = array('Domain','Request','Shortern','Ip');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->Auth->allow();
    }

	public function index() {
		echo "<pre>";print_r($_SERVER);echo "</pre>";
		echo 'I am here';
		exit;
	}

	public function request() {
		$this->set('title','Analytics');
        $this->set('subtitle','Request');

        if(isset($_GET['action'])){
            $this->request->data['analytics'] = _decode($_GET['action']);
        }
	}

	//Render charts
	public function renderchart($type){
		$response = array();
		switch($type){
			case 'key_request_chart_hour_wise' :
				$alias = isset($this->request->data['analytics']['alias']) && $this->request->data['analytics']['alias'] != '' ? $this->request->data['analytics']['alias'] : false; 
				$date = isset($this->request->data['analytics']['date']) && $this->request->data['analytics']['date'] != '' ? $this->request->data['analytics']['date'] : date('d/m/Y'); 
				if($alias){
					$response = $this->renderchart_key_request_chart_hour_wise($alias,$date);
				}else{

				}
			break;
		}

		$this->autoRender = false;
        $this->response->type('json');
        echo json_encode($response);
        exit;
	}

	//Render chart : Analytic chart with key and date
	private function renderchart_key_request_chart_hour_wise($alias,$date){
		$response = array();
		$datetime = strtotime($this->Common->mysqlDate($date,'dd/mm/yy'));
		$sdate = mktime(0,0,0,date('m',$datetime),date('d',$datetime),date('Y',$datetime));
		$edate = mktime(23,59,59,date('m',$datetime),date('d',$datetime),date('Y',$datetime));

		//Create time slot array
		$timeSlot = array();
		for($i=0;$i<24;$i++){
			$key = sprintf('%s',$i.':00 - '.$i.':59');
			$timeSlot[$key] = 0;
        }

		//Get data from request table according to key and date
		$data = $this->Request->find('all',
									array('conditions'=>array(
												'key'=>$alias,
												'created'=>array('$gte' => new MongoDate($sdate),
													             '$lt' => new MongoDate($edate))
											)
									));
		
		foreach ($data as $key => $value) {
			$created = $value['Request']['created'];
			$tmp = date('G:00 - G:59',$created->sec);
			$timeSlot[$tmp] = $timeSlot[$tmp] + 1; 
		}
		
		$response['color'] = $this->Common->randColor(1);
        $response['labels'] = array('Request');
        foreach ($timeSlot as $key => $value) {
        	$response['data'][] = array('y'=>$key,'Request'=>$value);
        }
        return $response;
	}

}