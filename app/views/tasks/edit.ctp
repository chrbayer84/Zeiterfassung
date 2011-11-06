<div class="tasks form">
<?php echo $this->Form->create('Task');?>
	<fieldset>
 		<legend><?php
				$action = !empty($this->params['origAction']) 
					? $this->params['origAction'] : $this->action;
				echo __(sprintf('%s %s', ucwords($action), 'Task'), true);
				?>
		</legend>
	<?php
		$javascript->link('datepicker.js', $inline = false);
		
		echo $this->Form->input('id');
		echo $this->Form->input('description');

		echo $this->Form->input('startdateOnly', array('type' => 'text',
			'size' => '15',	'class' => 'w8em format-d-m-y divider-dot transparency',
			'dateFormat' => 'DMY'));
		echo $this->Form->input('starttimeOnly', array('timeFormat' => '24'));
		echo $this->Form->input('enddateOnly',  array('type' => 'text',
			'size' => '15',	'class' => 'w8em format-d-m-y divider-dot transparency',
			'dateFormat' => 'DMY'));
		echo $this->Form->input('endtimeOnly', array('timeFormat' => '24'));
		echo $this->Form->input('ipaddress');
		echo $this->Form->input('user_id');		
		echo $this->Form->input('proxy_id', array('options' => $proxys, 'empty' => '(keine)'));
		echo $this->Form->input('client_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Task.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Task.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Tasks', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Clients', true), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client', true), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>