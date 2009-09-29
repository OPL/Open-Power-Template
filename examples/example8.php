<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	require('../lib/opt.components.php');
	try{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> httpHeaders(OPT_HTML); 

		require('db_connect.php'); 

		$selector = new selectComponent;
		$selector -> set('name', 'selected');

		$selector -> push(0, '---SELECT---');

		$r = mysql_query('SELECT id, name FROM categories ORDER BY id'); 
		while($row = mysql_fetch_assoc($r)) 
		{ 
			// add the next item
			$selector -> push($row['id'], $row['name']);
		}		

		if(isset($_GET['selected']))
		{
			$selector -> set('selected', $_GET['selected']);
			if($_GET['selected'] == 0)
			{
				$selector -> set('message', 'Invalid choice!');
			}
		}
		else
		{
			$selector -> set('selected', 0);
		}

		$tpl -> assign('selector', $selector);
		$tpl -> parse('example8.tpl');
		mysql_close(); 
	}catch(optException $exception){ 
		optErrorHandler($exception); 
	}
?>
