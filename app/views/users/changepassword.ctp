<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
 		<legend><?php __('Change Password'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('username');
		echo $this->Form->input('password');
		echo $this->Form->input('fullname');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
