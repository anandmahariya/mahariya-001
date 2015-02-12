<?php
set_include_path(APP."Vendor/" . PATH_SEPARATOR . get_include_path());
App::import('Vendor', 'iptolocation/iptolocation');

class TempController extends AppController {
    
    public $components = array('RequestHandler');
    
    public function index() {
        $detail = new IpToLocation('182.74.81.186');
        echo '<pre>';print_r($detail);echo '</pre>';
    }

}