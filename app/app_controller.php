<?php
class AppController extends Controller  {

	//Configuration
  var $components = array('Auth', 'Acl', 'RequestHandler', 'Session');
  var $helpers = array('Html','Form','Ajax','Javascript', 'Session', 'Time');
  
	function isAllowed() {
		$auth = $this->Session->read("Auth");
		if (!empty($auth['User']['username'])) {
		return $this->Acl->check(array('model' => 'Group',
			'foreign_key' => $this->params['named']['group']),
				 $this->params['named']['function']);
		}
		else {
			return false;
		}
	}
	
	function beforeFilter() {
	    //Configure AuthComponent
	    $this->Auth->authorize = 'actions';
	    $this->Auth->actionPath = 'controllers/';
	    $this->Auth->loginAction = array(
	    	'controller' => 'users',
	    	'action' => 'login');
	    $this->Auth->logoutRedirect = array(
	    	'controller' => 'users',
	     	'action' => 'login');
	    $this->Auth->loginRedirect = array(
	    	'controller' => 'tasks',
	    	'action' => 'index');
		$this->Auth->allowedActions = array('isAllowed');
		
		//make User::get work
		App::import('Model', 'User');
		User::store($this->Auth->user());
	}
}
?>