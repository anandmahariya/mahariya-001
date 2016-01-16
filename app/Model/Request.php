<?php
class Request extends AppModel {
    
	var $name = 'Request';
    var $primaryKey = '_id';
    var $useDbConfig = 'mongo';
    /*
    var $mongoSchema = array(
			'ip'=>array('type'=>'string'),
			'ip_long'=>array('type'=>'integer'),
			'as'=>array('type'=>'integer'),
			'registry'=>array('type'=>'string'),
			'as_name'=>array('type'=>'string'),
			'dns'=>array('type'=>'string'),
			'country_code'=>array('type'=>'string'),
			'country'=>array('type'=>'string'),
			'state'=>array('type'=>'string'),
			'city'=>array('type'=>'string'),
			'status'=>array('type'=>'string'),
			'created'=>array('type'=>'datetime'),
			'modified'=>array('type'=>'datetime'),
			);
            */
}