<?php
  //  --------------------------------------------------------------------  //
  //                        Open Power Template                             //
  //         Copyright (c) 2005-2007 Tomasz "Zyx" Jedrzejewski              //
  //     Copyright (c) 2008 Invenzzia Group, http://www.invenzzia.org/      //
  //  --------------------------------------------------------------------  //
  //  This program is free software; you can redistribute it and/or modify  //
  //  it under the terms of the GNU Lesser General Public License as        //
  //  published by the Free Software Foundation; either version 2.1 of the  //
  //  License, or (at your option) any later version.                       //
  //  --------------------------------------------------------------------  //
  //
  // $Id: opt.error.php 55 2006-06-10 12:00:48Z zyxist $
  
	// Error message codes

	class optException extends Exception
	{
		private $func;
		private $type;
		private $filename;
		public $directories;

		public function __construct($message = null, $code = null, $type=null, $file = null, $line = null, $function = null, $filename = null)
		{
			$this -> message = $message;
			$this -> code = $code;
			$this -> file = $file;
			$this -> line = $line;
			$this -> func = $function;
			$this -> type = $type;
			$this -> filename = $filename;
		} // end __construct();
		
		public function getFunction()
		{
			return $this -> func;
		} // end getFunction();
		
		public function getType()
		{
			return $this -> type;
		} // end getType();
		
		public function getFilename()
		{
			return $this -> filename;
		} // end getFilename();
	}
	
	function optErrorHandler(optException $exc)
	{
		echo '<div class="error opt">
			<p class="message"><strong> '.$exc->getType().' internal error #'.$exc->getCode().'</strong>:  '.$exc->getMessage().'</p>';
		if($exc->getCode() >= 100)
		{
			echo '<p class="location">Method: "<em>'.$exc->getFunction().'</em>"; Template: "<em>'.$exc->getFilename().'</em>"; File: "<em>'.$exc->getFile().'</em>"; Line: "<em>'.$exc->getLine().'</em>"</p>';
		}
		else
		{
			echo '<p class="location">Method: "<em>'.$exc->getFunction().'</em>"; File: "<em>'.$exc->getFile().'</em>"; Line: "<em>'.$exc->getLine().'</em>"</p>';			
		}
		echo '</div>';
		$trace = array_reverse($exc -> getTrace());
			
		
		echo '<div class="debug opt">
			<h3>Debug backtrace</h3>
			<table style="width: 70%; border: 1px solid #000000;">';
		echo '<tr>
			<td style="width: 20; background: #DDDDDD; font-weight: bold;">#</td>
			<td style="width: 30%; background: #DDDDDD; font-weight: bold;">In file</td>
			<td style="width: *; background: #DDDDDD; font-weight: bold;">Call</td>
			<td style="width: 7%; background: #DDDDDD; font-weight: bold;">Line</td>
		</tr>';
		foreach($trace as $number => $item)
		{
			if(isset($item['class']))
			{
				$callback = $item['class'].$item['type'].$item['function'];				
			}
			else
			{
				$callback = $item['function'];
			}
			echo '<tr>
				<td>'.$number.'</td>
				<td>'.basename($item['file']).'</td>
				<td>'.$callback.'</td>
				<td>'.$item['line'].'</td>
			</tr>';
		}
		echo '</table>';
		echo '<h3>Directories</h3>
			<table style="width: 50%; border: 1px solid #000000;">';
		echo '<tr>
			<td style="width: 30%; background: #DDDDDD; font-weight: bold;">Directory</td>
			<td style="width: 30%; background: #DDDDDD; font-weight: bold;">Value</td>
			<td style="width: 40%; background: #DDDDDD; font-weight: bold;">Status</td>
		</tr>';
			
		foreach($exc -> directories as $type => $data)
		{
			// checking status
			if($data == NULL)
			{
				$status = 'Not set';				
			}
			elseif(is_dir($data))
			{
				$status = '<span style="color: green; font-weight: bold;">Exists</span>';
			}
			else
			{
				$status = '<span style="color: red; font-weight: bold;">Not exists</span>';
			}
			echo '<tr>
				<td>'.$type.'</td>
				<td>'.$data.'</td>
				<td>'.$status.'</td>
			</tr>';
		}
		echo '</table>
		<p>Open Power Template '.OPT_VERSION.'</p>
		</div>';
	} // end optErrorHandler();

?>
