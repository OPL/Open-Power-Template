<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	try
	{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML); 

		require('db_connect.php'); 
		$r = mysql_query('SELECT id, name, description FROM products ORDER BY id'); 
		$list = array(); 
		while($row = mysql_fetch_row($r)) 
		{ 
			// add the next item 
			$list[] = array( 
				'id' => $row[0], 
				'name' => $row[1],
				'description' => $row[2]
			);
		}

		$tpl -> assign('products', $list); 
		$tpl -> parse('example2.tpl'); 
		mysql_close();
	}
	catch(optException $exception)
	{
		optErrorHandler($exception); 
	}
?>
