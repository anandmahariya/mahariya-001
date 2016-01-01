<?php
class Shortern extends AppModel {
    
	var $name = 'Shortern';
    var $primaryKey = '_id';
    var $useDbConfig = 'mongo';
    var $mongoSchema = array(
			'url' => array('type'=>'string'),
            'alias' => array('type'=>'string'),
			'domain' => array('type'=>'string'),
            'redirect' => array('type'=>'string'),
            'password' => array('type'=>'string'),
			'key'=>array('type'=>'string'),
			'uid'=>array('type'=>'number'),
            'status'=>array('type'=>'string'),
			'created'=>array('type'=>'datetime'),
			'modified'=>array('type'=>'datetime'),
			);
    
	public $validate = array(
       	'url' => array('url'=>array('rule' => array('url', true),
                                    'message' => 'You must provide a valid URL format.',
                                    'required' => true,),
                       'validateUrl'=>array('rule'=>array('validateUrl'),
                                            'message' => 'Not valid URL format.',),
                       ),
   );
    
    public function validateUrl(){
		$parse_url = parse_url($this->data['Shortern']['url']);
		if(in_array($parse_url['host'],array('localhost'))){
			return false;
		}
		return true;
	}
}