<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.api.php');
	
	try{ 
		$tpl = new optApi; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> assign('current_date', date('d.m.Y'));
		$tpl -> parse('example14.tpl');
	}
	catch(optException $exception)
	{ 
		optErrorHandler($exception); 
	}
?>
