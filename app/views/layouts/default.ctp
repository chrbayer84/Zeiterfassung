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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('Zeiterfassung Epple'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('cake.generic');
		echo $this->Html->css('buzzer');
		echo $this->Html->css('cfsnavbar');
		echo $this->Html->css('datepicker');
		
		$this->Javascript->includeScript('liveclock');
		$this->Javascript->includeScript('datepicker');
		echo $scripts_for_layout;		
	?>
</head>
<?php
    if (isset($bodyAttr)) {
        $bodyAttr = " $bodyAttr";
    } else {
        $bodyAttr = null;
    }
?>
<body<?php echo $bodyAttr; ?>>
	<div id="container">
		<div id="navigation">
		<ul>
			<li class="navigation_headline">
				<span><?php echo $this->Html->link(__('Zeiterfassung Epple', true), 'http://' . $_SERVER['SERVER_NAME'] .':' . $_SERVER['SERVER_PORT'] . '/zeiterfassung'); ?></span>
			</li>	
			<?php
			$auth = $this->Session->read("Auth");
			if (!empty($auth['User']['username'])) {
				echo '<li class="navigation_text">Benutzer:';
				echo $session->read('Auth.User.fullname');
				echo '</li>';
			}
			?>
						
			<?php echo $this->element('tasks_button', array(
        		'controller' => 'tasks', 'cache' => false,
				'key' => 'tasks_button')); ?>
					
			<?php echo $this->element('privileged_button', array(
        		'controller' => 'vacations', 'cache' => false,
				'key' => 'vacations_button')); ?>
			
			<?php echo $this->element('users_button', array(
        		'controller' => 'users', 'cache' => false,
				'key' => 'users_button')); ?>
        		
			<?php echo $this->element('menu_button', array(
        		'controller' => 'groups', 'cache' => false,
				'key' => 'groups_button')); ?>
        		
			<?php echo $this->element('menu_button', array(
        		'controller' => 'clients', 'cache' => false,
				'key' => 'clients_button')); ?>           		     		
			<?php
			$auth = $this->Session->read("Auth");
			if (!empty($auth['User']['username'])) {
				echo '<li>';
				echo $this->Html->link(__('Logout', true),
					array('controller' => 'users',	'action' => 'logout'));
				echo '</li>';
			}
			?>			
		</ul>
		</div>
		
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework', true), 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>