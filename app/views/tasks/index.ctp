<div id="clock">
<?php
	$javascript->link('liveclock.js', $inline = false);
	$javascript->codeBlock('
		/*
		Live Clock Script-
		By Mark Plachetta (astro@bigpond.net.au©) based on code from DynamicDrive.com
		For full source code, 100s more DHTML scripts, and Terms Of Use,
		visit http://www.dynamicdrive.com
		*/
	' , array('allowCache'=>true, 'safe'=>true, 'inline'=>false), null);
	//$javascript->event('body', 'onLoad', 'show_clock', null);	
	$this->set('bodyAttr', 'onload="show_clock()"');
?>
</div>

<div class="checkin_header form">
	<?php echo $this->Form->create('Task');?>
	<fieldset>
 		<legend><?php __('Checkin Task'); ?></legend>
	<?php
		echo $this->Form->input('client_id');
		echo $this->Form->input('description');
		echo $this->Form->input('proxy_id', array('options' => $proxys, 'empty' => '(keine)'));
	?>
	</fieldset>
	<?php
		$buzzer = array (
			'type' => 'image',			
			'src' => '/zeiterfassung/img/' . $buzzer_action . '.png',
			'div' => array(
        		'class' => $buzzer_action
			)
		); 
		echo $this->Form->end($buzzer);
	?>
</div>

<div class="tasks index">
	<h2><?php __('Tasks');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('starttime');?></th>
			<th><?php echo $this->Paginator->sort('endtime');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('proxy_id');?></th>
			<th><?php echo $this->Paginator->sort('client_id');?></th>
			<th><?php __('worktime')?></th>
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
		<td><?php echo $task['Task']['description']; ?>&nbsp;</td>
		<td><?php 
			if ($task['Task']['starttime'] !== '0000-00-00 00:00:00') {
				echo date("d.m.Y H:i", $this->Time->toUnix($task['Task']['starttime']));
			}			
		?>&nbsp;</td>
		<td><?php
			if ($task['Task']['endtime'] !== '0000-00-00 00:00:00') {
				echo date("d.m.Y H:i", $this->Time->toUnix($task['Task']['endtime']));
			}
		?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($task['User']['fullname'], array('controller' => 'users', 'action' => 'view', $task['User']['id'])); ?>
		</td>
		<td><?php echo $this->Html->link($task['Proxy']['fullname'], array('controller' => 'users', 'action' => 'view', $task['Proxy']['id'])); ?>
			&nbsp;
		</td>
		<td>
			<?php echo $this->Html->link($task['Client']['name'], array('controller' => 'clients', 'action' => 'view', $task['Client']['id'])); ?>
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
	</tr>
	<?php endforeach; ?>
		<tr <?php
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			echo $class;?>>
				<td colspan=6 class="SummaryWorktime">Gesamtarbeitszeit:</td>
				<td colspan=2 class="SummaryWorktime">
				<?php
				$sumWorkHours += (int)( $sumWorkMinutes / 60);
				$sumWorkMinutes %= 60;
				echo str_pad($sumWorkHours, 2 ,'0', STR_PAD_LEFT) . ":" .
					str_pad($sumWorkMinutes, 2 ,'0', STR_PAD_LEFT) . "h";					
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
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>