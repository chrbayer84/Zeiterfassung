<div class="users view">
<h2><?php  __('User');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Username'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['username']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Fullname'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['fullname']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Group'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($user['Group']['name'], array('controller' => 'groups', 'action' => 'view', $user['Group']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
    <ul>
	    <li><?php echo $this->Html->link(__('Edit User', true), array('action' => 'edit', $user['User']['id'])); ?> </li>
	    <li><?php echo $this->Html->link(__('Delete User', true), array('action' => 'delete', $user['User']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['id'])); ?> </li>
	    <li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index')); ?> </li>
	    <li><?php echo $this->Html->link(__('New User', true), array('action' => 'add')); ?> </li>
	    <li><?php echo $this->Html->link(__('List Groups', true), array('controller' => 'groups', 'action' => 'index')); ?> </li>
	    <li><?php echo $this->Html->link(__('New Group', true), array('controller' => 'groups', 'action' => 'add')); ?> </li>
	    <li><?php echo $this->Html->link(__('List Tasks', true), array('controller' => 'tasks', 'action' => 'index')); ?> </li>
	    <li><?php echo $this->Html->link(__('New Task', true), array('controller' => 'tasks', 'action' => 'add')); ?> </li>
	    <li><?php echo $this->Html->link(__('List Vacations', true), array('controller' => 'vacations', 'action' => 'index')); ?> </li>
	    <li><?php echo $this->Html->link(__('New Vacation', true), array('controller' => 'vacations', 'action' => 'add')); ?> </li>
    </ul>
</div>

<div class="period">
<h3><?php __('Select Period'); ?></h3>
	<?php
		$javascript->link('datepicker.js', $inline = false);
		echo $this->Form->create(false, array('url' => '/users/view/' . $user['User']['id']));
		echo $this->Form->hidden('user_id', array('default' => $user['User']['id']));
		
		echo $this->Form->input('Beginn', array('size' => '15',
			'class' => 'w8em format-d-m-y divider-dot transparency',
			'default' => $startdate));
		echo $this->Form->input('Ende', array('size' => '15',
			'class' => 'w8em format-d-m-y divider-dot transparency',
			'default' => $enddate));		
		echo $this->Form->end(__('Ok', true));
	?>
</div>

<div class="related">
	<h3><?php __('Related Tasks');?></h3>
	<?php if (!empty($user['Task'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Description'); ?></th>
		<th><?php __('Starttime'); ?></th>
		<th><?php __('Endtime'); ?></th>
		<th><?php __('Ipaddress'); ?></th>
		<th><?php __('Client Id'); ?></th>
		<th><?php __('Proxy'); ?></th>
		<th><?php __('worktime')?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		$sumWorkHours = 0;
		$sumWorkMinutes = 0;
		
		foreach ($tasks as $task):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $task['Task']['description'];?></td>
			<td><?php 
				if ($task['Task']['starttime'] !== '0000-00-00 00:00:00') {
					echo date("d.m.Y H:i", $this->Time->toUnix($task['Task']['starttime']));
				}			
			?></td>
			<td><?php
				if ($task['Task']['endtime'] !== '0000-00-00 00:00:00') {
					echo date("d.m.Y H:i", $this->Time->toUnix($task['Task']['endtime']));
				}
			?></td>
			<td><?php echo $task['Task']['ipaddress'];?></td>
			<td>
				<?php echo $this->Html->link($task['Client']['name'], array('controller' => 'clients', 'action' => 'view', $task['Client']['id'])); ?>
			</td>			
			<td>
				<?php echo $this->Html->link($task['Proxy']['fullname'], array('controller' => 'users', 'action' => 'view', $task['Proxy']['id'])); ?>
			</td>
			<td>				
				<?php								
				$worktime = calculateWorktime($task['Task']['starttime'], 
						$task['Task']['endtime'] );
				$workHours = $worktime['hours'];
				$workMinutes = $worktime['minutes'];		
				$sumWorkHours += $workHours;
				$sumWorkMinutes += $workMinutes;
				echo str_pad($workHours, 2 ,'0', STR_PAD_LEFT) . ":" .
					str_pad($workMinutes, 2 ,'0', STR_PAD_LEFT) . "h";
				?>
			</td>
			<td class="actions">
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'tasks', 'action' => 'edit', $task['Task']['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'tasks', 'action' => 'delete', $task['Task']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $task['Task']['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
		<tr <?php
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
				<td colspan=6 class="SummaryWorktime"><?php __('Total work time')?> vom
				<?php echo $startdate?> bis 
				<?php echo $enddate ?>
				f&uuml;r <?php echo $user['User']['fullname']?>:</td>
				<td class="SummaryWorktime">
				<?php
				$sumWorkHours += (int)( $sumWorkMinutes / 60);
				$sumWorkMinutes %= 60;
				echo str_pad($sumWorkHours, 2 ,'0', STR_PAD_LEFT) . ":" .
					str_pad($sumWorkMinutes, 2 ,'0', STR_PAD_LEFT) . "h";
				?>
				</td>
				<td>alle Daten im Zeitraum <?php echo
					$this->Html->link('exportieren',
						array(	'controller' => 'users',
								'action' => 'export',
								$startdate,
								$enddate,
								$user['User']['id'] . ".csv")); ?>.</td>
				<td></td>				
		</tr>
		<tr <?php
			$overtime = calculateOvertime($startdate, $enddate, $sumWorkHours, $sumWorkMinutes, true);
			$overtimeString = $overtime['isOver'] ? __('Overtime', true) : __('Undertime', true);
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
			<td colspan=6 class="SummaryWorktime">
				<?php echo $overtimeString ?>:</td>
			<td class="SummaryWorktime">
			<?php
				echo $overtime['overtime'];
			?></td>
			<td></td>
			<td></td>
		</tr>
		<tr <?php
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
			<td colspan=6 class="SummaryWorktime"><?php				
				__('Nominal work time')?>:</td>
			<td class="SummaryWorktime">
			<?php				
				echo $overtime['totalWorktime'];
			?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<p>
	<?php
		echo $this->Paginator->counter(array(
		'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
		));
	?>	</p>
	
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 	| 	<?php echo $this->Paginator->numbers();?>
 		|	<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Task', true), array('controller' => 'tasks', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Vacations');?></h3>
	<?php if (!empty($user['Vacation'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Date'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Vacation'] as $vacation):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php 
				if ($vacation['date'] !== '0000-00-00 00:00:00') {
					echo date("d.m.Y", $this->Time->toUnix($vacation['date']));
				}?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'vacations', 'action' => 'delete', $vacation['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $vacation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Vacation', true), array('controller' => 'vacations', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
