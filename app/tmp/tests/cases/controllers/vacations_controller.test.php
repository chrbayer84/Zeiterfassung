<?php
/* Vacations Test cases generated on: 2010-10-10 18:10:51 : 1286727951*/
App::import('Controller', 'Vacations');

class TestVacationsController extends VacationsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class VacationsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.vacation', 'app.user', 'app.group', 'app.task', 'app.client');

	function startTest() {
		$this->Vacations =& new TestVacationsController();
		$this->Vacations->constructClasses();
	}

	function endTest() {
		unset($this->Vacations);
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