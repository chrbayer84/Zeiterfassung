<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php
	$group = $session->read('Auth.User.group_id');
	$allowed =  $this->requestAction(array('controller' => 'users',
		'action' => 'isAllowed'), array('named' => 
			array('group' => $group, 'function' => 'Users/changepassword')));
	if ($allowed) {
		echo '<li>';
		echo $this->Html->link(__('Users', true),
			array('controller' => 'users', 'action' => 'changepassword'));		
		echo '<ul>';
		$allowed =  $this->requestAction(array('controller' => 'users',
			'action' => 'isAllowed'), array('named' => 	array('group' => $group,
				'function' => ucfirst('users') . '/add')));
		if ($allowed) {
			echo '<li>';
			echo $this->Html->link(__('New ' . substr(ucfirst('users'),
			 	0, -1), true), array('controller' => 'users', 'action' => 'add'));
			echo '</li>';
		}
		$allowed =  $this->requestAction(array('controller' => 'users',
			'action' => 'isAllowed'), array('named' => 
				array('group' => $group, 'function' => 'Users/index')));
		if ($allowed) {
			echo '<li>';
			echo $this->Html->link(__('List Users', true),
				array('controller' => 'users', 'action' => 'index'));
			echo '</li>';
		}
		echo '</ul></li>';
	}
?>