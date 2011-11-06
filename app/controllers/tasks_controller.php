<?php
class TasksController extends AppController {

	//TODO summierung der arbeitszeiten
	//TODO Supervisor-berechtigungen fixen
	//TODO über-/unterstundenanzeige
	//TODO soll-arbeitszeit 160 stunden im monat --> überstundenabbau
	
	var $name = 'Tasks';
	
	function index_all() {
		$this->Task->recursive = 0;
		$this->paginate = array(
			'limit' => 10,
		);
		$this->set('tasks', $this->paginate('Task'));
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
    }
   
	function index ($id = null) {
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
					
				//$this->data['Task']['endtime'] = date('c');
				$this->data['Task']['enddateOnly'] = date('d.m.Y');
				$this->data['Task']['endtimeOnly'] = date('H:i');
			}
			else {
				//kein aktiver task gefunden, neuen task erstellen
				$this->data['Task']['ipaddress'] = $_SERVER['REMOTE_ADDR'];
				$this->data['Task']['user_id'] = User::get('id');
				//$this->data['Task']['starttime'] = date('c', time() - 300);
				$this->data['Task']['startdateOnly'] = date('d.m.Y');
				$this->data['Task']['starttimeOnly'] = date('H:i', time() - 300);
			}
						
			//check wheter begin or endtime
			if ($this->Task->save($this->data)) {
				$this->Session->setFlash(__('The task has been saved', true));
				//sleep (10);
				$this->redirect(array(	'controller' => 'tasks',
	    								'action' => 'index'));
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.', true));
			}
		}
		//read all data from database if id was passed
		if ($id && empty($this->data)) {
			$this->data = $this->Task->read(null, $id);
		}
		
		$clients = $this->Task->Client->find('list');
		$users = $this->Task->User->find('list');		
	    $proxys = $this->Task->Proxy->find('list');
					
		$this->set(compact('clients', 'users', 'proxys'));
		
		//Tabelle		
		$this->Task->recursive = 0;
		$this->paginate = array(
	        'conditions' => array('Task.user_id' => User::get('id')),
			'order' => array('Task.starttime' => 'desc')
	    );
	    $this->set('tasks', $this->paginate('Task'));
	}

	function edit($id = null) {		
		if (!empty($this->data)) {
			if (empty($this->data['Task']['startdateOnly']) ||
					empty($this->data['Task']['starttimeOnly'])) {
				$this->Session->setFlash(__('Please specify a start date and time.', true));					
			} else if ($this->Task->save($this->data)) {
				$this->Session->setFlash(__('The task has been saved', true));
				$this->redirect(array('action' => 'index_all'));
			} else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.', true));
			}
		}
		if ($id && empty($this->data)) {
			$this->data = $this->Task->read(null, $id);
		}
		$clients = $this->Task->Client->find('list');
		$users = $this->Task->User->find('list');		
		$proxys = $this->Task->Proxy->find('list');
					
		$this->set(compact('clients', 'users', 'proxys'));
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