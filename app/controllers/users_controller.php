<?php
class UsersController extends AppController {

	var $name = 'Users';
		
	function setup_permissions() {
		if (!Configure::read('debug')) {
			return $this->_stop();
		}
		$log = array();
		$group =& $this->User->Group;	
	    
	    //Mitarbeiter-Berechtigungen
	    $group->id = 3;
	    $this->Acl->deny($group, 'controllers');        
	    $this->Acl->allow($group, 'controllers/Tasks/view');
	    $this->Acl->allow($group, 'controllers/Tasks/index');
	    $this->Acl->allow($group, 'controllers/Tasks/add');
	    //$this->Acl->allow($group, 'controllers/Tasks/checkin');	    
	    $this->Acl->allow($group, 'controllers/Tasks/view');
	    	    
	    $this->Acl->allow($group, 'controllers/Users/login');
	    $this->Acl->allow($group, 'controllers/Users/changepassword');
	    $this->Acl->allow($group, 'controllers/Users/logout');
	    
	    $this->Acl->allow($group, 'controllers/Vacations/index');
	    $this->Acl->allow($group, 'controllers/Vacations/edit');
	    $this->Acl->allow($group, 'controllers/Vacations/add');
	    $this->Acl->allow($group, 'controllers/Vacations/view');
	    $this->Acl->allow($group, 'controllers/Vacations/delete');
	    
	    $this->Acl->allow($group, 'controllers/Clients/view');
	    $this->Acl->allow($group, 'controllers/Groups/view');
	    
	    
	    $log[] = 'Created Acls for Mitarbeiter';

	    //Supervisor-Berechtigungen
	    $group->id = 2;
	    $this->Acl->deny($group, 'controllers');
	    //$this->Acl->deny($group, 'controllers/Tasks/add');
	   	$this->Acl->allow($group, 'controllers/Tasks/index'); //_all
	    $log[] = 'Created Acls for Supervisor';
	 
	    //Administrator-Berechtigungen
	    $group->id = 1;     
	    $this->Acl->allow($group, 'controllers');
	 	$log[] = 'Created Acls for Administrator';
	    
		if(count($log)>0) {
			debug($log);
		}
	}
	
	function build_aro() {
		if (!Configure::read('debug')) {
			return $this->_stop();
		}
		$log = array();

		$aro =& $this->Acl->Aro;
		$root = $aro->node('');
		if (!$root) {
			$aro->create(array('parent_id' => null, 'model' => 'Group', 'alias' => 'Mitarbeiter', 'foreign_key' => '6'));
			$root = $aro->save();
			$root['Aro']['id'] = $aro->id; 
			$log[] = 'Created Aro node for Mitarbeiter';
		} else {
			$root = $root[0];
		}

		$aro->create(array('parent_id' => null, 'model' => 'Group', 'alias' => 'Supervisor', 'foreign_key' => '5'));
		$root = $aro->save();
		$root['Aro']['id'] = $aro->id; 
		$log[] = 'Created Aro node for Supervisor';
		
		$aro->create(array('parent_id' => null, 'model' => 'Group', 'alias' => 'Administrator', 'foreign_key' => '4'));
		$root = $aro->save();
		$log[] = 'Created Aro node for Administrator';
		
		if(count($log)>0) {
			debug($log);
		}	
	}
    
	/**
	 * Rebuild the Acl based on the current controllers in the application
	 *
	 * @return void
	 */
	function build_aco() {
		if (!Configure::read('debug')) {
			return $this->_stop();
		}
		$log = array();

		$aco =& $this->Acl->Aco;
		$root = $aco->node('controllers');
		if (!$root) {
			$aco->create(array('parent_id' => null, 'model' => null, 'alias' => 'controllers'));
			$root = $aco->save();
			$root['Aco']['id'] = $aco->id; 
			$log[] = 'Created Aco node for controllers';
		} else {
			$root = $root[0];
		}   

		App::import('Core', 'File');
		$Controllers = Configure::listObjects('controller');
		$appIndex = array_search('App', $Controllers);
		if ($appIndex !== false ) {
			unset($Controllers[$appIndex]);
		}
		$baseMethods = get_class_methods('Controller');
		$baseMethods[] = 'buildAcl';
		
		$Plugins = $this->_getPluginControllerNames();
		$Controllers = array_merge($Controllers, $Plugins);

		// look at each controller in app/controllers
		foreach ($Controllers as $ctrlName) {
			$methods = $this->_getClassMethods($this->_getPluginControllerPath($ctrlName));

			// Do all Plugins First
			if ($this->_isPlugin($ctrlName)){
				$pluginNode = $aco->node('controllers/'.$this->_getPluginName($ctrlName));
				if (!$pluginNode) {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginName($ctrlName)));
					$pluginNode = $aco->save();
					$pluginNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginName($ctrlName) . ' Plugin';
				}
			}
			// find / make controller node
			$controllerNode = $aco->node('controllers/'.$ctrlName);
			if (!$controllerNode) {
				if ($this->_isPlugin($ctrlName)){
					$pluginNode = $aco->node('controllers/' . $this->_getPluginName($ctrlName));
					$aco->create(array('parent_id' => $pluginNode['0']['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginControllerName($ctrlName)));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginControllerName($ctrlName) . ' ' . $this->_getPluginName($ctrlName) . ' Plugin Controller';
				} else {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $ctrlName));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $ctrlName;
				}
			} else {
				$controllerNode = $controllerNode[0];
			}

			//clean the methods. to remove those in Controller and private actions.
			foreach ($methods as $k => $method) {
				if (strpos($method, '_', 0) === 0) {
					unset($methods[$k]);
					continue;
				}
				if (in_array($method, $baseMethods)) {
					unset($methods[$k]);
					continue;
				}
				$methodNode = $aco->node('controllers/'.$ctrlName.'/'.$method);
				if (!$methodNode) {
					$aco->create(array('parent_id' => $controllerNode['Aco']['id'], 'model' => null, 'alias' => $method));
					$methodNode = $aco->save();
					$log[] = 'Created Aco node for '. $method;
				}
			}
		}
		if(count($log)>0) {
			debug($log);
		}
		exit;
	}

	function _getClassMethods($ctrlName = null) {
		App::import('Controller', $ctrlName);
		if (strlen(strstr($ctrlName, '.')) > 0) {
			// plugin's controller
			$num = strpos($ctrlName, '.');
			$ctrlName = substr($ctrlName, $num+1);
		}
		$ctrlclass = $ctrlName . 'Controller';
		$methods = get_class_methods($ctrlclass);

		// Add scaffold defaults if scaffolds are being used
		$properties = get_class_vars($ctrlclass);
		if (array_key_exists('scaffold',$properties)) {
			if($properties['scaffold'] == 'admin') {
				$methods = array_merge($methods, array('admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete'));
			} else {
				$methods = array_merge($methods, array('add', 'edit', 'index', 'view', 'delete'));
			}
		}
		return $methods;
	}

	function _isPlugin($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) > 1) {
			return true;
		} else {
			return false;
		}
	}

	function _getPluginControllerPath($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0] . '.' . $arr[1];
		} else {
			return $arr[0];
		}
	}

	function _getPluginName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}

	function _getPluginControllerName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[1];
		} else {
			return false;
		}
	}

	/**
	 * Get the names of the plugin controllers ...
	 * 
	 * This function will get an array of the plugin controller names, and
	 * also makes sure the controllers are available for us to get the 
	 * method names by doing an App::import for each plugin controller.
	 *
	 * @return array of plugin names.
	 *
	 */
	function _getPluginControllerNames() {
		App::import('Core', 'File', 'Folder');
		$paths = Configure::getInstance();
		$folder =& new Folder();
		$folder->cd(APP . 'plugins');

		// Get the list of plugins
		$Plugins = $folder->read();
		$Plugins = $Plugins[0];
		$arr = array();

		// Loop through the plugins
		foreach($Plugins as $pluginName) {
			if ($pluginName !== '.svn') {
				// Change directory to the plugin
				$didCD = $folder->cd(APP . 'plugins'. DS . $pluginName . DS . 'controllers');
				// Get a list of the files that have a file name that ends
				// with controller.php
				$files = $folder->findRecursive('.*_controller\.php');
	
				// Loop through the controllers we found in the plugins directory
				foreach($files as $fileName) {
					// Get the base file name
					$file = basename($fileName);
	
					// Get the controller name
					$file = Inflector::camelize(substr($file, 0, strlen($file)-strlen('_controller.php')));
					if (!preg_match('/^'. Inflector::humanize($pluginName). 'App/', $file)) {
						if (!App::import('Controller', $pluginName.'.'.$file)) {
							debug('Error importing '.$file.' for plugin '.$pluginName);
						} else {
							/// Now prepend the Plugin name ...
							// This is required to allow us to fetch the method names.
							$arr[] = Inflector::humanize($pluginName) . "/" . $file;
						}
					}
				}
			}
		}
		return $arr;
	}
		
	function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->autoRedirect = false;
		//TODO wieder weg
		//$this->Auth->allow(array('*'));
		//dashboard
		$this->Auth->allowedActions = array('isAllowed');
		$this->Auth->loginRedirect = array(
			'controller' => 'tasks',
			'action' => 'index');
    }
    
	function login() {
		//$this->redirect('/', null, false);
		//clearCache();		
	}
	
	function logout() {		
		$this->redirect($this->Auth->logout());
	}	
	
	function index() {
		
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id && !empty($this->data)) {
			$id = $this->data['user_id'];
		}
		
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
		
		
//		$startdate = mktime(0, 0, 0, date("m"), 1,   date("Y"));
//		$enddate = mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
//		$this->set('startdate',  date('d.m.Y', $startdate));
//		$this->set('enddate',  date('d.m.Y', $enddate));		
//		
//		// Zeitraum angegeben		
//		if (!empty($this->data)) {			
//			$startdate = strtotime($this->data['Beginn']); 
//			$enddate = strtotime($this->data['Ende']);
//		}
//		
//  		$month = date('m',$startdate); 
//  		$day = date('d',$startdate); 
//  		$year = date('Y',$startdate); 
//  		$startdate = date('Y-m-d H:i:s', mktime( 
//        	null, 
//            null,
//            null, 
//            $month, 
//            $day, 
//            $year));
//         
//  		$month = date('m',$enddate); 
//  		$day = date('d',$enddate); 
//  		$year = date('Y',$enddate); 
//  		$enddate = date('Y-m-d H:i:s', mktime( 
//            null, 
//            null,
//            null, 
//            $month, 
//            $day, 
//            $year));
//  		                  
//		$this->set('user', $this->User->read(null, $id));
//		$this->paginate = array(
//			'conditions' => array(	'Task.user_id' => $id
//								  ,	'Task.starttime >= ' => $startdate
//								  ,	'Task.endtime <=' => $enddate
//		), 
//			// TODO limit does not work with the startdate and enddate boxes,
//			// need ajax which will cause new problems with the export
////			'limit' => 10,
//			'order' => 'Task.starttime ASC'
//		);
//		
//		$this->set('tasks', $this->paginate('Task'));		
	}

	function edit($id = null) {		
		//debug('startime' . $this['Task']['starttime_only']);
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if ($id && empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function changepassword() {
		$id = User::get('id');
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
		
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

		$this->set('user', $this->User->read(null, $id));

		if ($limit != null) {
			$this->paginate = array(
				'conditions' => array(	'Task.user_id' => $id
									  ,	'Task.starttime >= ' => $startdate
									  ,	'Task.endtime <=' => $enddate
				), 
				// TODO limit does not work with the startdate and enddate boxes,
				// need ajax which will cause new problems with the export
	//			'limit' => 10,
				'order' => 'Task.starttime ASC',
				'limit' => 2147483647, 
			);
		}
		else {
			$this->paginate = array(
				'conditions' => array(	'Task.user_id' => $id
									  ,	'Task.starttime >= ' => $startdate
									  ,	'Task.endtime <=' => $enddate
				), 
				// TODO limit does not work with the startdate and enddate boxes,
				// need ajax which will cause new problems with the export
//				'limit' => 10000,
				'order' => 'Task.starttime ASC',
				'limit' => 2147483647, 
			);
		}

		$this->set('tasks', $this->paginate('Task'));
	}
	
	function export($startdate = 0, $enddate = 0, $id = null) {		
		$this->User->id = $id;
		$name = $this->User->field('username') . '_' . $startdate . '-' . $enddate . '.csv';
		
		$startdate = strtotime($startdate);
		$enddate = strtotime($enddate);

		// Stop Cake from displaying action's execution time
		$debugValue = Configure::read('debug');	
		Configure::write('debug', 0);		
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
	 			'Client',
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