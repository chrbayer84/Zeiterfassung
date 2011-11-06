<div class="vacations form">
<?php
	$javascript->link('liveclock.js', $inline = false);
	echo $this->Form->create('Vacation');
?>
	<fieldset>
 		<legend><?php
				$action = !empty($this->params['origAction']) 
					? $this->params['origAction'] : $this->action;
				echo __(sprintf('%s %s', ucwords($action), 'Vacation'), true);
				?>
		</legend>
	<?php
		$javascript->link('datepicker.js', $inline = false);
		
		echo $this->Form->input('id');
		echo $this->Form->input('startdateOnly', array('type' => 'text',
			'size' => '15',	'class' => 'w8em format-d-m-y divider-dot transparency',
			'dateFormat' => 'DMY'));
		echo $this->Form->input('enddateOnly',  array('type' => 'text',
			'size' => '15',	'class' => 'w8em format-d-m-y divider-dot transparency',
			'dateFormat' => 'DMY'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Vacation.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Vacation.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Vacations', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>