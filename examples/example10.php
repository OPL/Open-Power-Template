<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
 
	try
	{ 
		$tpl = new optClass;
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> cache = './cache/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML);

		$tpl -> cacheStatus(true, 30);
		
		if(!$tpl -> isCached('example10.tpl'))
		{
			require('db_connect.php'); 
			$r = mysql_query('SELECT id, name, description FROM products ORDER BY id');
			$list = array();
			while($row = mysql_fetch_assoc($r)) 
			{ 
				// add the next item 
				$list[] = array( 
					'id' => $row['id'], 
					'name' => $row['name'],
					'description' => $row['description']
				);
			}
			$tpl -> assign('products', $list);
			mysql_close();
		}
		// cache this template result for 30 seconds
		$tpl -> parse('example10.tpl'); 
	}
	catch(optException $exception)
	{ 
		optErrorHandler($exception); 
	}
?>
