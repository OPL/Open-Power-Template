<?php
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
 
	try{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML); 
    
		$tpl -> parse('example6.tpl'); 
	}catch(optException $exception){ 
		optErrorHandler($exception); 
	}
?>
