<?php
class DashboardController extends AppController {
    
    public function beforerender(){
        $this->set('title','Dashboard');
        $this->set('subtitle','Control panel');
    }
    
    public function index() {
        CakeLog::info('sdfsdf', array('tracking'));
    }
}