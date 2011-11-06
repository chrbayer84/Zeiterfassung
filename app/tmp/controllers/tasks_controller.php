<?php
class TasksController extends AppController {

	var $name = 'Tasks';
		
	function index() {		
		$this->Task->recursive = 0;
		
		$this->paginate = array(
	        'conditions' => array('Task.user_id' => User::get('id')),
	    );
	    $this->set('tasks', $this->paginate('Task'));
	}
	
	function index_all() {
		$this->Task->recursive = 0;
		$this->set('tasks', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid task', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('task', $this->Task->read(null, $id));
	}
	
	function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->autoRedirect = false;
		//TODO wieder weg
		$this->Auth->allow(array('*'));

		//dashboard
		
    }
   
	function checkin ($id = null) {
		$open_record = $this->Task->find('open_record');
		
		if (!empty($open_record)) {
			// noch aktiver task gefunden, buzzer = stop		
			$this->set('buzzer_action', 'buzzer_stop');
			$buzzer_action = false;
			$id = $open_record[0]['Task']['id'];
		}
		else {
			//kein aktiver task gefunden, neuen task erstellen
			$this->set('buzzer_action', 'buzzer_start');
			$buzzer_action = true;
		}
		
		if (!empty($this->data)) {
			//benutzer hat daten eingetragen, entweder ersteintrag oder edit
			
			if (!empty($open_record)) {
				// noch aktiver task gefunden, buzzer = stop
					
				$this->data['Task']['endtime'] = date('c');
			}
			else {
				//kein aktiver task gefunden, neuen task erstellen
				$this->data['Task']['ipaddress'] = $_SERVER['REMOTE_ADDR'];
				$this->data['Task']['user_id'] = User::get('id');
				$this->data['Task']['starttime'] = date('c');
			}
			
			//debug($this->data);
	
			
			//check wheter begin or endtime
			
			if ($this->Task->save($this->data)) {
				$this->Session->setFlash(__('The task has been saved', true));
				//sleep (10);
				$this->redirect(array(	'controller' => 'tasks',
	    								'action' => 'checkin'));
				/*
				if ($buzzer_action) {
					$this->set('buzzer_action', 'buzzer_stop');
				}
				else {
					$this->set('buzzer_action', 'buzzer_start');
				}
				*/
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.', true));
			}
		}
		//read all data from database if id was passed
		if ($id && empty($this->data)) {
			$this->data = $this->Task->read(null, $id);
		}
		
		//debug('database read data: ' . $this->data);
		
		$clients = $this->Task->Client->find('list');
		$users = $this->Task->User->find('list');
		$this->set(compact('clients', 'users'));
		
		//Tabelle		
		$this->Task->recursive = 0;
		$this->paginate = array(
	        'conditions' => array('Task.user_id' => User::get('id')),
			'order' => array('Task.starttime' => 'desc')
	    );
	    $this->set('tasks', $this->paginate('Task'));
		
	}

	function add() {
		if (!empty($this->data)) {
			$this->Task->create();
			if ($this->Task->save($this->data)) {
				$this->Session->setFlash(__('The task has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.', true));
			}
		}
		$clients = $this->Task->Client->find('list');
		$users = $this->Task->User->find('list');
		$this->set(compact('clients', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid task', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Task->save($this->data)) {
				$this->Session->setFlash(__('The task has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Task->read(null, $id);
		}
		$clients = $this->Task->Client->find('list');
		$users = $this->Task->User->find('list');
		$this->set(compact('clients', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for task', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Task->delete($id)) {
			$this->Session->setFlash(__('Task deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Task was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
				 
}
?>