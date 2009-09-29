<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
 
	try
	{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> cache = './cache/';
		$tpl -> cacheExpire = 30;
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML); 

		$tpl -> assign('current_date', date('d.m.Y, H:i:s')); 
		
		// cache this template result for 30 seconds
		$tpl -> cacheStatus(true);
		$tpl -> parse('example9.tpl');
	}
	catch(optException $exception)
	{ 
		optErrorHandler($exception); 
	}
?>
