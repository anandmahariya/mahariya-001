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
				$sdate = isset($this->request->data['analytics']['sdate']) && $this->request->data['analytics']['sdate'] != '' ? $this->request->data['analytics']['sdate'] : date('d/m/Y'); 
				$edate = isset($this->request->data['analytics']['edate']) && $this->request->data['analytics']['edate'] != '' ? $this->request->data['analytics']['edate'] : date('d/m/Y'); 
				if($alias){
					$response = $this->renderchart_key_request_chart_hour_wise($alias,$sdate,$edate);
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
	private function renderchart_key_request_chart_hour_wise($alias,$sdate,$edate){
		$response = array();
		$sdatetime = strtotime($this->Common->mysqlDate($sdate,'dd/mm/yy'));
		$edatetime = strtotime($this->Common->mysqlDate($edate,'dd/mm/yy'));

		$sdate = mktime(0,0,0,date('m',$sdatetime),date('d',$sdatetime),date('Y',$sdatetime));
		$edate = mktime(23,59,59,date('m',$edatetime),date('d',$edatetime),date('Y',$edatetime));

		//Get data from request table according to key and date
		$data = $this->Request->find('all',
									array('conditions'=>array(
												'key'=>$alias,
												'created'=>array('$gte' => new MongoDate($sdate),
													             '$lt' => new MongoDate($edate))
											)
									));

		
		/*************************   ceate request hour base chart  ********************/
		$chartRequest = array();
		//Create time slot array
		$timeSlot = array();
		while($sdate <= $edate){

			$k1 = date('d/m/Y',$sdate);
			for($i=0;$i<24;$i++){
				$k2 = sprintf('%s',$i.':00 - '.$i.':59');
				$timeSlot[$k2][$k1] = 0;
	        }
			//Increase sdate to one date
			$sdate = $sdate + (60*60*24);
		}

		//fill database value in timeslot array 
		foreach ($data as $key => $value) {
			$created = $value['Request']['created'];
			$k1 = date('d/m/Y',$created->sec);
			$k2 = date('G:00 - G:59',$created->sec);
			$timeSlot[$k2][$k1] = $timeSlot[$k2][$k1] + 1; 
		}
		
		//create graph ykeys
        $tmp = reset($timeSlot);
        $chartRequest['ykeys'] = array_keys($tmp);
        $chartRequest['labels'] = array_keys($tmp);
        $chartRequest['color'] = $this->Common->randColor(count($tmp));

       	foreach ($timeSlot as $k1 => $v1) {
        	$chartRequest['data'][] = array_merge(array('y'=>$k1),$v1);
        }

        $response['chart1'] = array('element'=>'request-analytics-chart-hour-wise',
									'resize'=>true,
									'xLabelAngle'=>45,
									'parseTime'=>false,
									'hideHover'=>'auto',
									'xkey'=>'y',
									'labels'=>$chartRequest['labels'],
									'color'=>$chartRequest['color'],
									'ykeys'=>$chartRequest['ykeys'],
									'data'=>$chartRequest['data']);
		

        //Create location chart
        $location = array();
        foreach ($data as $key => $value) {
        	$country = isset($value['Request']['location']['country']) ? $value['Request']['location']['country'] : '-' ;
        	$state = isset($value['Request']['location']['state']) ? $value['Request']['location']['state'] : '-';
        	$city = isset($value['Request']['location']['city']) ? $value['Request']['location']['city'] : '-';
        	if(isset($location[$country][$state][$city])){
        		$location[$country][$state][$city] += 1;
        	}else{
        		$location[$country][$state][$city] = 0;
        	}
        } 

        $total = 0;
        $table = '<table class="table table-striped"><tbody><tr><th>Country</th><th>State</th><th>City</th><th>Visitors</th></tr>';
        foreach($location as $country=>$val1){
        	foreach($val1 as $state=>$val2){
        		foreach($val2 as $city=>$val3){
            			$total += $val3;
            $table .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>',$country,
                                $state,
                                $city,$val3);
        }}}
        $table .= sprintf('<tr><td></td><td></td><td><b>Total</b></td><td>%d</td></tr>',$total);
        $table .= '</table>';

        $response['location'] = $table;

		return $response;
	}

}