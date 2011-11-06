<?php
class Vacation extends AppModel {
	var $name = 'Vacation';
	var $validate = array(
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
		'startdateOnly' => array(
			'datestring' => array(
				'rule' => '/[0-9]{2}[\-\/\.][0-9]{2}[\-\/\.][0-9]{2,4}$/i',
				'allowEmpty' => false,
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
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function __findOwn($options) {
    	$options = array('conditions' => array('user_id' => User::get('id')), $options);
    	
    	return parent::find('all', $options);
    }
    
	function beforeValidate() {
		if (!$this->isUnique(array('date', 'user_id'), false)) {
			return false;
		}		
		else return true;
	}
}
?>