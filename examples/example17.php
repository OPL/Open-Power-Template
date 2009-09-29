<?php
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	
	function optResourceDatabase(optClass $tpl, $title, $compiledTime = NULL)
	{
		if(is_null($compiledTime))
		{
			$r = mysql_query('SELECT `code` FROM `templates` WHERE `title` = \''.$title.'\'');
		}
		else
		{
			$r = mysql_query('SELECT `code` FROM `templates` WHERE `title` = \''.$title.'\' AND `lastmod` > \''.$compiledTime.'\'');
		}
		if($row = mysql_fetch_row($r))
		{
			return $row[0];
		}
		return false;	
	} // end optResourceDatabase();
 
	try{ 
		require('db_connect.php'); 
		$tpl = new optClass;
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML);
		$tpl -> registerResource('db', 'Database');
    
    	$tpl -> assign('current_date', date('d.m.Y'));
		$tpl -> parse('db:example17'); 
	}catch(optException $exception){ 
		optErrorHandler($exception); 
	}
?>
