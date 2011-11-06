<?php
    echo $session->flash('auth');
    echo $form->create('User', array('action' => 'login'));
    echo $this->Form->inputs(array(
    	'legend' => __('Login', true),
    	'username',
    	'password'
   	));
    echo $form->end('Login');
?>
