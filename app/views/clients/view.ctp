<div class="clients view">
<h2><?php  __('Client');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Client', true), array('action' => 'edit', $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Client', true), array('action' => 'delete', $client['Client']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Clients', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tasks', true), array('controller' => 'tasks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Task', true), array('controller' => 'tasks', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="period">
<h3><?php __('Select Period'); ?></h3>
	<?php
		$javascript->link('datepicker.js', $inline = false);
		echo $this->Form->create(false, array('url' => '/clients/view/' . $client['Client']['id']));
		echo $this->Form->hidden('client_id', array('default' => $client['Client']['id']));
		
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
	<?php if (!empty($client['Task'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Description'); ?></th>
		<th><?php __('Starttime'); ?></th>
		<th><?php __('Endtime'); ?></th>
		<th><?php __('Ipaddress'); ?></th>		
		<th><?php __('User Id'); ?></th>
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
				<?php echo $this->Html->link($task['User']['fullname'], array('controller' => 'users', 'action' => 'view', $task['User']['id'])); ?>
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
			<?php echo $startdate ?> bis 
			<?php echo $enddate ?>
			f&uuml;r <?php echo $client['Client']['name']?>:</td>
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
					array(	'controller' => 'clients',
							'action' => 'export',
							$startdate,
							$enddate,
							$client['Client']['id'] . ".csv")); ?>.</td>
			<td></td>	
		</tr>
		<!--
		<tr < ?php
			$overtime = calculateOvertime($startdate, $enddate, $sumWorkHours, $sumWorkMinutes, true);
			$overtimeString = $overtime['isOver'] ? __('Overtime', true) : __('Undertime', true);
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
			<td colspan=6 class="SummaryWorktime">
				< ?php echo $overtimeString ?>:</td>
			<td class="SummaryWorktime">
			< ?php
				echo $overtime['overtime'];
			?></td>
			<td></td>
			<td></td>
		</tr>
		<tr < ?php
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
			<td colspan=6 class="SummaryWorktime">< ?php				
				__('Nominal work time')?>:</td>
			<td class="SummaryWorktime">
			< ?php				
				echo $overtime['totalWorktime'];
			?></td>
			<td></td>
			<td></td>
		</tr>
		-->
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
