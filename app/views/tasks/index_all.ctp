<div class="tasks index">
	<h2><?php __('Tasks');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('starttime');?></th>
			<th><?php echo $this->Paginator->sort('endtime');?></th>		
			<th><?php echo $this->Paginator->sort('ipaddress');?></th>
			<th><?php echo $this->Paginator->sort('client_id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>			
			<th><?php echo $this->Paginator->sort('proxy_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
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
			?>&nbsp;
		</td>
		<td><?php
		 	if ($task['Task']['endtime'] !== '0000-00-00 00:00:00') {
				echo date("d.m.Y H:i", $this->Time->toUnix($task['Task']['endtime']));
			}
			?>&nbsp;
		</td>
		<td><?php echo $task['Task']['ipaddress']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($task['Client']['name'], array('controller' => 'clients', 'action' => 'view', $task['Client']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($task['User']['fullname'], array('controller' => 'users', 'action' => 'view', $task['User']['id'])); ?>
		</td>
		<td><?php echo $this->Html->link($task['Proxy']['fullname'], array('controller' => 'users', 'action' => 'view', $task['Proxy']['id'])); ?>
			&nbsp;
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $task['Task']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $task['Task']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $task['Task']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
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
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Task', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Clients', true), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client', true), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>