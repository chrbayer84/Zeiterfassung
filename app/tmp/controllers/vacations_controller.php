<?php
class VacationsController extends AppController {

	var $name = 'Vacations';

	function index() {
		$this->Vacation->recursive = 0;
		
		$this->paginate = array(
	        'conditions' => array('Vacation.user_id' => User::get('id')),
	    );
	    $this->set('vacations', $this->paginate('Vacation'));
	}
	
	function index_all() {
		$this->Task->recursive = 0;
		$this->set('vacations', $this->paginate());
	}

	function edit($id = null) {
		if (!empty($this->data)) {
			//aktuelle user_id reinschreiben, falls diese noch nicht existiert			
			$user_id = $this->Vacation->read('user_id', $id);
			//aus db lesen, data enthält nur gepostete daten
			if (empty($user_id)) {
				$this->data['Vacation']['user_id'] = User::get('id');
			}
			if ($this->Vacation->save($this->data)) {
				$this->Session->setFlash(__('The vacation has been saved', true));
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The vacation could not be saved. Please, try again.', true));
			}
		}
		if ($id && empty($this->data)) {
			$this->data = $this->Vacation->read(null, $id);
			
		}
		$users = $this->Vacation->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for vacation', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Vacation->delete($id)) {
			$this->Session->setFlash(__('Vacation deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Vacation was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>