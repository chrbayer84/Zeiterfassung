<?php
class Task extends AppModel {
	var $name = 'Task';

	var $validate = array(
		'startdateOnly' => array(
			'datestring' => array(
				'rule' => '/[0-9]{2}[\-\/\.][0-9]{2}[\-\/\.][0-9]{2,4}$/i',
				'allowEmpty' => true,
				'message' => 'Please specify a valid date.',
	),
	),
		'enddateOnly' => array(
			'datestring' => array(
				'rule' => '/[0-9]{2}[\-\/\.][0-9]{2}[\-\/\.][0-9]{2,4}$/i',
				'allowEmpty' => true,
				'message' => 'Please specify a valid date.',
	),
	),
		'starttimeOnly' =>  array(
			'timestring' => array(
				'rule' => '/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i',
				'allowEmpty' => true,
				'message' => 'Please specify a valid time.',
	),
	),
		'endtimeOnly' =>  array(
			'timestring' => array(
				'rule' => '/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i',
				'allowEmpty' => true,
				'message' => 'Please specify a valid time.',
	),
	),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please give a short description of your task.',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'ipaddress' => array(
			'notempty' => array(
				'rule' => array('notempty'),
	),
			'validip' => array(
				'rule' => array('ip'),
				'message' => 'Please specify a valid IP address.',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
	//'message' => 'Your custom message here',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
	//'message' => 'Your custom message here',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			),
		'Proxy' => array(
			'className'    => 'User',
            'foreignKey'   => 'proxy_id',
            'conditions'   => '',
            'order'        => '',
			'fields' 	   => ''            
			)
			);

	//limit => 1 und desc
	function __findOpenRecord($options) {
		$options = array('conditions' => array(
    'endtime' => null,
    'user_id' => User::get('id')),    	
    'limit' => 1, $options);
		return parent::find('all', $options);
	}

	function __findOwn($options) {
		$options = array('conditions' => array('user_id' => User::get('id')), $options);

		return parent::find('all', $options);
	}

	function afterFind($results) {
		// Create a dateOnly pseudofield using date field.
		foreach ($results as $key => $val) {
			if (isset($val['Task']['starttime']) && $val['Task']['starttime']
			!== '0000-00-00 00:00:00') {
				$results[$key]['Task']['startdateOnly'] = date('d.m.Y',
				strtotime($val['Task']['starttime']));
				$results[$key]['Task']['starttimeOnly'] = date('H:i',
				strtotime($val['Task']['starttime']));
				$starttime_unix = strtotime($val['Task']['starttime']);
			}
			if (isset($val['Task']['endtime']) && $val['Task']['endtime']
			!== '0000-00-00 00:00:00') {
				$results[$key]['Task']['enddateOnly'] = date('d.m.Y',
				strtotime($val['Task']['endtime']));
				$results[$key]['Task']['endtimeOnly'] = date('H:i',
				strtotime($val['Task']['endtime']));
				$endtime_unix = strtotime($val['Task']['endtime']);
			}
			$worktime = '';
			if (isset($endtime_unix) && isset($starttime_unix) &&
			$endtime_unix > $starttime_unix) {
				$worktime = $endtime_unix - $starttime_unix - 3600;
			}
			$results[$key]['Task']['worktime'] = $worktime;
		}
		return $results;
	}

	function beforeSave() {
		if (!empty($this->data['Task']['startdateOnly']) &&
		!empty($this->data['Task']['starttimeOnly'])) {
			$timestamp = strtotime($this->data['Task']['startdateOnly']);
			$month = date('m', $timestamp);
			$day = date('d', $timestamp);
			$year = date('Y', $timestamp);
				
			$timestamp = strtotime($this->data['Task']['starttimeOnly']);
			$hour = date('H', $timestamp);
			$minute = date('i', $timestamp);

			$this->data['Task']['starttime'] = date('Y-m-d H:i:s', mktime(
			$hour,
			$minute,
			null,
			$month,
			$day,
			$year));
		}

		if (!empty($this->data['Task']['enddateOnly']) &&
		!empty($this->data['Task']['endtimeOnly'])) {
			$timestamp = strtotime($this->data['Task']['enddateOnly']);
			$month = date('m', $timestamp);
			$day = date('d', $timestamp);
			$year = date('Y', $timestamp);
				
			$timestamp = strtotime($this->data['Task']['endtimeOnly']);
			$hour = date('H', $timestamp);
			$minute = date('i', $timestamp);

			$this->data['Task']['endtime'] = date('Y-m-d H:i:s', mktime(
			$hour,
			$minute,
			null,
			$month,
			$day,
			$year));
		}
		else {
			$this->data['Task']['endtime'] = 0;
		}

		return true;
	}
}
?>