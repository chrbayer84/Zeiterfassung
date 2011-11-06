<?php
class ClientsController extends AppController {

	var $name = 'Clients';
	// Include the RequestHandler, it makes sure the proper layout and views files are used
	var $components = array('RequestHandler');

	function _displayData($id, $limit, $startdate, $enddate) {
		if (!$id && !empty($this->data)) {
			$id = $this->data['client_id'];
		}
		
		
		$month = date('m',$startdate);
		$day = date('d',$startdate);
		$year = date('Y',$startdate);
		$startdate = date('Y-m-d H:i:s', mktime(
		null,
		null,
		null,
		$month,
		$day,
		$year));
			
		$month = date('m',$enddate);
		$day = date('d',$enddate);
		$year = date('Y',$enddate);
		// add one day for the select statement to work
		$enddate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s',mktime(
		null,
		null,
		null,
		$month,
		$day,
		$year)) . " +1 day"));

		$this->set('client', $this->Client->read(null, $id));

		if ($limit != null) {
			$this->paginate = array(
				'conditions' => array(	'Task.client_id' => $id,
				'Task.starttime >= ' => $startdate,
				'Task.endtime <=' => $enddate
			),
			// TODO limit does not work with the startdate and enddate boxes,
			// need ajax which will cause new problems with the export
//			'limit' => $limit,
			'limit' => 2147483647, 
			'order' => 'Task.starttime ASC'
			);
		}
		else {
			$this->paginate = array(
				'conditions' => array(	'Task.client_id' => $id,
				'Task.starttime >= ' => $startdate,
				'Task.endtime <=' => $enddate,
			),
			//'fields'=>array('Location.id', 'Location.name', 'Location.type', 'Location.parent_id', 'Location.longitude', 'Location.latitude', 'Location.confirmed_id'),
			'order' => 'Task.starttime ASC',
			'limit' => 2147483647
			);
		}

		$this->set('tasks', $this->paginate('Task'));
	}

	function index() {
		$this->Client->recursive = 0;
		$this->set('clients', $this->paginate());
	}

	function view($id = null) {
		if (!isset($startdate) && !isset($enddate)) {
			$startdate = mktime(0, 0, 0, date("m"), 1,   date("Y"));
			$enddate = mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
		}

		// Zeitraum angegeben
		if (!empty($this->data)) {
			$startdate = strtotime($this->data['Beginn']);
			$enddate = strtotime($this->data['Ende']);
		}
		// make current dates available in view
		$this->set('startdate',  date('d.m.Y', $startdate));
		$this->set('enddate',  date('d.m.Y', $enddate));
		
		$this->_displayData($id, 10, $startdate, $enddate);
	}

	function edit($id = null) {
		if (!empty($this->data)) {
			if ($this->Client->save($this->data)) {
				$this->Session->setFlash(__('The client has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.', true));
			}
		}
		if ($id && empty($this->data)) {
			$this->data = $this->Client->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for client', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Client->delete($id)) {
			$this->Session->setFlash(__('Client deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Client was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}

	function export($startdate = 0, $enddate = 0, $id = null) {
		$this->Client->id = $id;
		$name = $this->Client->field('name') . '_' . $startdate . '-' . $enddate . '.csv';
		
		$startdate = strtotime($startdate);
		$enddate = strtotime($enddate);
		
		// Stop Cake from displaying action's execution time		
		$debugValue = Configure::read('debug');	
		Configure::write('debug',0);
		
		$mimeType = 'application/vnd.ms-excel';
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: ".$mimeType);
		header("Content-Disposition: attachment; filename=\"".$name."\";");
		if (strpos($mimeType, 'text') !== false) header("Content-Transfer-Encoding: binary");
		//		header("Content-Length: ".@filesize($file));
		set_time_limit(0);			

		// Define column headers for CSV file, in same array format as the data itself
		$headers = array(
                'Description',
				'Starttime',
				'Endtime',
	 			'Ipaddress',
	 			'Fullname',
				'Proxy',
				'Worktime',
				);
		$this->set('headers', $headers);
		$this->set('startdate', date("d.m.Y", $startdate));
		$this->set('enddate', date("d.m.Y", $enddate));
		
		$this->_displayData($id, null, $startdate, $enddate);
		Configure::write('debug', $debugValue);
	}
}
?>