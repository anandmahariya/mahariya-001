<?php
class SearchController extends AppController {
    
    public $components = array('RequestHandler','Common','GCS');
    public $start = 0;
    public $limit = 10;
    public $format = 'json';
    
    public function beforefilter(){
        $this->set('title','Search');
        $this->set('subtitle','Control panel');
        
        $this->start = isset($_GET['s']) ?  $_GET['s'] : $this->start;
        $this->limit = isset($_GET['l']) ?  $_GET['l'] : $this->limit;
        $this->format = isset($_GET['f']) ?  $_GET['f'] : $this->format;
    }
    
    /*****
     * Query = q
     * start = s
     * limit = l
     * format = f
     ******/
    
    public function index() {
        //https://www.googleapis.com/customsearch/v1?key=AIzaSyDETpXCuhV0RsUtqiM5i8GB0EZ_v_bLNLs&cx=013036536707430787589:_pqjad5hr1a&q=hi&alt=json
        echo '<pre>';print_r($_GET);echo '</pre>';
        exit;
    }
    
    private function google($query){
        $search_results = $this->GCS->run_search($term, $start, true); 
    }
    
}