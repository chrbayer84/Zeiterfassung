<?php
/* Tasks Test cases generated on: 2010-10-10 18:10:46 : 1286727946*/
App::import('Controller', 'Tasks');

class TestTasksController extends TasksController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class TasksControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.task', 'app.client', 'app.user', 'app.group', 'app.vacation');

	function startTest() {
		$this->Tasks =& new TestTasksController();
		$this->Tasks->constructClasses();
	}

	function endTest() {
		unset($this->Tasks);
		ClassRegistry::flush();
	}

	function testIndex() {

	}

	function testView() {

	}

	function testAdd() {

	}

	function testEdit() {

	}

	function testDelete() {

	}

}
?>