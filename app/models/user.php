<?php
class User extends AppModel {
	var $name = 'User';
	var $displayField = 'fullname';
	var $actsAs = array('Acl' => array('type' => 'requester'));
	var $validate = array(
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
	//'message' => 'Your custom message here',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
	//'message' => 'Your custom message here',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'fullname' => array(
			'notempty' => array(
				'rule' => array('notempty'),
	//'message' => 'Your custom message here',
	//'allowEmpty' => false,
	//'required' => false,
	//'last' => false, // Stop validation after this rule
	//'on' => 'create', // Limit validation to 'create' or 'update' operations
	),
	),
		'group_id' => array(
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
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
			)
			);

	var $hasMany = array(
		'Task' => array(
			'className' => 'Task',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
			),		
		'TaskAsProxy' => array(
			'className' => 'Task',
			'foreignKey' => 'proxy_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
			),		
		'Vacation' => array(
			'className' => 'Vacation',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
			)
			);
	
	//in AppController::beforeFilter:
	//App::import('Model', 'User');
	//User::store($this->Auth->user());

	//Static get methonds for user
	function &getInstance($user=null) {
		static $instance = array();

		if ($user) {
			$instance[0] =& $user;
		}

		if (!$instance) {
			trigger_error(__("User not set.", true), E_USER_WARNING);
			return false;
		}

		return $instance[0];
	}

	function store($user) {
		if (empty($user)) {
			return false;
		}

		User::getInstance($user);
	}

	function get($path) {
		$_user =& User::getInstance();

		$path = str_replace('.', '/', $path);
		if (strpos($path, 'User') !== 0) {
			$path = sprintf('User/%s', $path);
		}

		if (strpos($path, '/') !== 0) {
			$path = sprintf('/%s', $path);
		}

		$value = Set::extract($path, $_user);

		if (!$value) {
			return false;
		}

		return $value[0];
	}

	/*
	 *  Gruppen-ACL
	 */
	function bindNode($user) {
		return array('Group' => array('id' => $user['User']['group_id']));
	}

	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		if (isset($this->data['User']['group_id'])) {
			$groupId = $this->data['User']['group_id'];
		} else {
			$groupId = $this->field('group_id');
		}
		if (!$groupId) {
			return null;
		} else {
			return array('Group' => array('id' => $groupId));
		}
	}
}
?>