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
				$user_id = User::get('id');
			}		
			
			if (!empty($this->data['Vacation']['startdateOnly'])) {
				$timestamp = strtotime($this->data['Vacation']['startdateOnly']);
				$startmonth = date('m', $timestamp);
				$startday = date('d', $timestamp);
				$startyear = date('Y', $timestamp);
		
				$newdate = array(
					'0' => array(
						'date' => date('Y-m-d H:i:s', mktime(
							null,
							null,
							null,
							$startmonth,
							$startday,
							$startyear)),
						'user_id' => $user_id,
					)
				);
							
				if (!empty($this->data['Vacation']['enddateOnly'])) {
					$timestamp = strtotime($this->data['Vacation']['enddateOnly']);
					$endmonth = date('m', $timestamp);
					$endday = date('d', $timestamp);
					$endyear = date('Y', $timestamp);
									
					$startdate = gmmktime(0, 0, 0, $startmonth, $startday + 1, $startyear);
					$enddate = gmmktime(0, 0, 0, $endmonth,$endday, $endyear);
					$index = 1;
					for ($i = $startdate; $i <= $enddate; $i += 86400) {						
						$seconddate = array(
							$index => array(
								'date' => gmdate('Y-m-d H:i:s', $i),
								'user_id' => $user_id,
							)
						);
						$index++;
						$newdate = array_merge($newdate, $seconddate);												
					}					
				}
			}
			$dates = array('Vacation' => $newdate); 
			if ($this->Vacation->saveAll($dates['Vacation'])) {
				$this->Session->setFlash(__('The vacation has been saved', true));
				$this->redirect(array('action' => 'index'));
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
			$this->redirect($this->referer());
		}
		if ($this->Vacation->delete($id)) {
			$this->Session->setFlash(__('Vacation deleted', true));
			$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Vacation was not deleted', true));
		$this->redirect($this->referer());
	}
}
?>