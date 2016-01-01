<?php
class Domain extends AppModel {
    
    public $validate = array(
       	'name' => array('checkUnique'=>array('rule' => array('checkUnique'),
                                             'message' => 'This Domain already registered.',
                                             'required'=>true,
                                             'on' => 'create'),
                                             'domain'=>array('rule'=>'url','message'=>'Not a valid URL'),
                                             )
    );
    
    public function checkUnique() {
        $count = $this->find('count', array('conditions' => array('name' => $this->data['Domain']['name'])));
        if($count > 0) return false;
        return true;
    }
}