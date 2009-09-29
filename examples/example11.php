<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	try
	{
		$tpl = new optClass;
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		// Enable XML-style delimiters
		$tpl -> xmlsyntaxMode = 1;
		// Enable strict XML syntax for the instructions
		$tpl -> strictSyntax = 1;
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
		if(rand(0, 10) < 5)
		{
			$tpl -> assign('border' , 'border="1"');
		}
		$tpl -> parse('example11.tpl'); 
		mysql_close();
	}
	catch(optException $exception)
	{ 
		optErrorHandler($exception); 
	}
?>
