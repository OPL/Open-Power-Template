<?php
/*
 *  OPEN POWER LIBS <http://libs.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Support.php 22 2008-12-03 11:32:29Z zyxist $
 */

	class Opt_Support
	{
		const CACHED_TPL = 1;
		const STANDARD_TPL = 2;
		
		static private $_viewCount = 1;
		static private $_cplCount = 1;
		
		static private $_totalTime = 0;
		static private $_xmlMemory = 0;
		static private $_optWarnings = false;
		
		static public function initDebugConsole($tpl)
		{
			Opl_Debug_Console::addList('opt_options', 'OPT Options and settings');
			Opl_Debug_Console::addList('opt_stats', 'OPT Stats');
			Opl_Debug_Console::addTable('opt_views', 'OPT: Executed views', array('#', 'View', 'Output', 'Time', 'Cached'));
			Opl_Debug_Console::addTable('opt_compiled', 'OPT: Compiled templates', array('#', 'Template', 'Estimated XML tree memory'));
			Opl_Debug_Console::addListOptions('opt_options', $tpl->getConfig());
		} // end initDebugConsole();

		static public function addView($view, $outputName, $time, $cached)
		{
			self::$_totalTime += $time;
			Opl_Debug_Console::addTableItem('opt_views', array(self::$_viewCount++, $view, $outputName,  number_format($time, 5).' s', ($cached) ? 'Yes' : 'No'));
		} // end addDebugTemplate();
		
		static public function addCompiledTemplate($template, $memory)
		{
			self::$_xmlMemory += $memory;
			Opl_Debug_Console::addTableItem('opt_compiled', array(self::$_cplCount++, $template, number_format($memory).' b'));
		} // end addCompiledTemplate();

		static public function updateTimers()
		{
			Opl_Debug_Console::addListOption('opt_stats', 'Executed views: ', self::$_viewCount-1);
			Opl_Debug_Console::addListOption('opt_stats', 'Compiled templates: ', self::$_cplCount-1);
			Opl_Debug_Console::addListOption('opt_stats', 'Total template time: ', number_format(self::$_totalTime, 5).' s');
			if(self::$_cplCount-1 > 0)
			{
				Opl_Debug_Console::addListOption('opt_stats', 'Average XML memory per template: ', number_format(self::$_xmlMemory / (self::$_cplCount-1)).' b');
			}
		} // end updateTimers();
		
		static public function warning($text)
		{
			if(!self::$_optWarnings)
			{
				Opl_Debug_Console::addTable('opt_warnings', 'OPT: Warnings', array('Message'));
			}
			Opl_Debug_Console::addTableItem('opt_warnings', array($text));
		} // end warning();
	} // end Opt_Support;
