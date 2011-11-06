<?php
// File: /app/views/clients/csv/export.ctp

function printValues($tasks) {
	$i = 0;
	$sumWorkHours = 0;
	$sumWorkMinutes = 0;
	foreach ($tasks as $task) {
		$line ='';
		$line = $line . escapeQuotes($task['Task']['description']);
		if ($task['Task']['starttime'] !== '0000-00-00 00:00:00') {
			$line = $line . escapeQuotes(date("d.m.Y H:i", strtotime($task['Task']['starttime'])));
		}
		else {
			$line = $line . escapeQuotes("");
		}
		if ($task['Task']['endtime'] !== '0000-00-00 00:00:00') {
			$line = $line . escapeQuotes(date("d.m.Y H:i", strtotime($task['Task']['endtime'])));
		}
		else {
			$line = $line . escapeQuotes("");
		}
		$line = $line . escapeQuotes($task['Task']['ipaddress']);
		$line = $line . escapeQuotes($task['User']['fullname']);
		$line = $line . escapeQuotes($task['Proxy']['fullname']);
		
		$worktime = calculateWorktime($task['Task']['starttime'], 
						$task['Task']['endtime'] );
		$workHours = $worktime['hours'];
		$workMinutes = $worktime['minutes'];		
		$sumWorkHours += $workHours;
		$sumWorkMinutes += $workMinutes;
		$line = $line . escapeQuotes(str_pad($workHours, 2 ,'0', STR_PAD_LEFT) . ":" .
			str_pad($workMinutes, 2 ,'0', STR_PAD_LEFT) . "h");
			
		// Echo all values in a row comma separated
		echo ($line . "\n");
	}
	return array('sumWorkHours' => $sumWorkHours, 'sumWorkMinutes' => $sumWorkMinutes);
}

printHeaders($headers);
$sumWorkTime = printValues($tasks);
printWorkHours($headers, $sumWorkTime, $startdate, $enddate, false);

?>