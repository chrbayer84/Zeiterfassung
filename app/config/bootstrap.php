<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
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
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

ini_set('date.timezone', 'Europe/Berlin');
/*
 function daysDiff($startDate, $endDate)
 {
 // Parse dates for conversion
 $startArry = date_parse($startDate);
 $endArry = date_parse($endDate);
 // Convert dates to Julian Days
 $start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
 $end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);

 // Return difference
 return round(($end_date - $start_date), 0);
 }
 */

function calculateWorktime($starttime, $endtime) {
	if (isset($starttime)
	&& $endtime !== '0000-00-00 00:00:00'
	&& isset($endtime)
	&& $endtime!== '0000-00-00 00:00:00') {

		$days = null;

		$starttime_unix = strtotime($starttime);
		$endtime_unix = strtotime($endtime);

		$diff = $endtime_unix - $starttime_unix;
		if ($diff > 0) {
			if( $days=intval((floor($diff/86400))) )
			$diff = $diff % 86400;
			if( $hours=intval((floor($diff/3600)) + 24 * $days) )
			$diff = $diff % 3600;
			if( $minutes=intval((floor($diff/60))) )
			$diff = $diff % 60;
			$diff = intval( $diff );

			return array('hours' => $hours, 'minutes' => $minutes);
		}
		else {
			return array('hours' => 0, 'minutes' => 0);
		}
	}
}

function calculateOvertime($startdate, $enddate, $hours, $minutes, $format = true) {
	$now = strtotime($startdate);
	$totalWorktime = 0;
	while (date("Y-m-d", $now) != date("Y-m-d", strtotime($enddate))) {
		$day_index = date("w", $now);
		if (!($day_index == 0 || $day_index == 6)) {
			// add weekdays
			$totalWorktime += 8;
		}
		$now = strtotime(date("Y-m-d", $now) . "+1 day");
	}

	$overtimeHours = $totalWorktime - $hours;
	// if overtimeHours > 0: minus hours -> red
	// substract minutes from overtimeHours
	$overtimeHours = -($overtimeHours);
	$overtimeMinutes = 0;
	if ($overtimeHours < 0) {
		// minus hours
		if ($minutes > 0) {
			$overtimeHours += 1;
			$overtimeMinutes = 60 - $minutes;
		}
		if ($format) {
			$overtime = '<font color=red>' . $overtimeHours . ":"
				. ($overtimeMinutes >= 10 ? "" : "0") . $overtimeMinutes . 'h</font>';
		}
		else {
			$overtime = $overtimeHours . ":"
				. ($overtimeMinutes >= 10 ? "" : "0") . $overtimeMinutes . 'h';
		}
	}
	else {
		// overtime
		$overtimeMinutes = $minutes;
		if ($format) {
			$overtime = '<font color=green>' . $overtimeHours . ":"
				. ($overtimeMinutes >= 10 ? "" : "0")  . $overtimeMinutes . 'h</font>';
		}
		else {
			$overtime = $overtimeHours . ":"
				. ($overtimeMinutes >= 10 ? "" : "0")  . $overtimeMinutes . 'h';
		}
	}
	return array('overtime' => $overtime, 'totalWorktime' => $totalWorktime . ':00h', 'isOver' => $overtimeHours >= 0);
}

function printHeaders($headers) {
	$line ='';
	foreach ($headers as $value)
	{
		$value = "\"".__($value, true)."\",";
		$line =  $line . $value;
	}
	$line = substr($line, 0, strlen($line) - 1);
	echo ($line . "\n");
}

function escapeQuotes ($value) {
	//escape "-characters
	$offset = 0;
	$positionFound = strpos($value, '"', $offset);
	while ($positionFound !== false) {
		$lengthToEnd = strlen($value) - $positionFound;
		$value = substr($value, 0, $positionFound)
		. '"' .
		substr($value, $positionFound, $lengthToEnd);

		$offset = $positionFound + 2;
		$positionFound = strpos($value, '"', $offset);
	}

	$value = "\"".$value."\",";
	return $value;
}

function printWorkHours($headers, $sumWorkTime, $startdate, $enddate, $printOverTime = true) {
	$line ='';
	for ($i = 0; $i < sizeof($headers) - 2; $i++)
	{
		$value = "\"\",";
		$line = $line . $value;
	}
	
	// insert work hour strings
	$line = $line . '"' . __("Total work time", true) . ':",';
	
	$sumWorkHours = $sumWorkTime['sumWorkHours'];
	$sumWorkMinutes = $sumWorkTime['sumWorkMinutes'];
	
	$sumWorkHours += (int)( $sumWorkMinutes / 60);
				$sumWorkMinutes %= 60;
	
	$line =  $line . '"' . str_pad($sumWorkHours, 2 ,'0', STR_PAD_LEFT) . ":" .
					str_pad($sumWorkMinutes, 2 ,'0', STR_PAD_LEFT) . "h" . '"';
	
	echo ($line . "\n");
	if ($printOverTime) {
		// new line with overtime
		$overtime = calculateOvertime($startdate, $enddate, $sumWorkHours, $sumWorkMinutes, false);
		$overtimeString = $overtime['isOver'] ? __('Overtime', true) : __('Undertime', true);
					
		$line ='';
		for ($i = 0; $i < sizeof($headers) - 2; $i++)
		{
			$value = "\"\",";
			$line = $line . $value;
		}
	
		// insert overtime string				
		$line = $line . '"' . $overtimeString . ':",';
		$line = $line . '"' . $overtime['overtime'] . '"';
		echo ($line . "\n");
		
		// new line with nominal work time 			
		$line ='';
		for ($i = 0; $i < sizeof($headers) - 2; $i++)
		{
			$value = "\"\",";
			$line = $line . $value;
		}
		// insert nominal work time 
		$line = $line . '"' . __("Nominal work time", true) . ':",';				
						
		$line = $line . '"' . $overtime['totalWorktime'] . '"';
		echo ($line . "\n");
	}
}