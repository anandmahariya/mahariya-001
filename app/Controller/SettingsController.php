<?php
class SettingsController extends AppController {
    
    public function beforefilter(){
        $this->set('title','Settings');
        $this->set('subtitle','Control panel');
    }
    
    public function index() {

    }
    
    public function getscript() {
        
    }
}