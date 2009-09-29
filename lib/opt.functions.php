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
  // $Id: opt.functions.php 41 2006-02-26 16:48:09Z zyxist $

	function optPredefFirstof()
	{
		$args = func_get_args();
		$cnt = sizeof($args);
		for($i = 0; $i < $cnt; $i++)
		{
			if(!empty($args[$i]))
			{
				return $args[$i];
			}
		}
	} // end optPredefFirstof();

	function optPredefSpacify($string, $delim = ' ')
	{
		$ns = '';
		$len = strlen($string);

		for($i = 0; $i < $len; $i++)
		{
			$ns .= $string[$i];
			if($i + 1 < $len)
			{
				$ns .= $delim;
			}
		}
		return $ns;
	} // end optPredefSpacify();

	function optPredefIndent($string, $num, $with = ' ')
	{
		return preg_replace('/([\\r\\n]{1,2})/', '$1'.str_repeat($with, $num), $string);
	} // end optPredefIndent();

	function optPredefStrip($string)
	{
		return preg_replace('/\s\s+/', ' ', $string);
	} // end optPredefStrip();

	function optPredefTruncate($string, $length, $etc = '', $break = true)
	{
		if($length == 0)
		{
			return '';
		}
		$strlen = strlen($string);
		if($strlen > $length)
		{
			if(!$break)
			{
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length));
			}
			return substr($string, 0, $length).$etc;
		}
		return $string;
	} // end optPredefTruncate();
 
	function optPredefParseInt($tpl, $bigint)
	{
		//return number_format($bigint, $tpl -> parseintDecimals, $tpl -> parseintDecPoint, $tpl -> parseintThousands);
		return rtrim(number_format($bigint, $tpl -> parseintDecimals, $tpl -> parseintDecPoint, $tpl -> parseintThousands), $tpl->parseintDecPoint.'0');
	} // end optPredefParseInt();
	
	function optPredefWordwrap($tpl, $text, $width, $break = 0)
	{
		if(is_string($break))
		{
			$break = str_replace('\\n', "\n", $break);
		}
		else
		{
			$break = "\n";
		}
 
		return wordwrap($text, $width, $break);
	} // end optPredefWordwrap();
	
	function optPredefApply($tpl, $group, $item)
	{
		$args = func_get_args();
		unset($args[0]);
		unset($args[1]);
		unset($args[2]);
		$tpl -> i18n[$group][$item] = vsprintf($tpl -> i18n[$group][$item], $args);
	} // end optPredefApply();
	
	function optPredefCycle($tpl)
	{
		$args = func_get_args();
	
		static $i;
		if(!isset($i))
		{
			$i = 1;
		}
		
		if($i >= count($args))
		{
			$i = 1;
		}
		
		return $args[$i++];
	} // end optPredefCycle();
?>
