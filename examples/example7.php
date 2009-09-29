<?php
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	require('../lib/opt.components.php');
 
	try{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;
		$tpl -> compileCacheDisabled = 1;
		$tpl -> httpHeaders(OPT_HTML); 
		
		require('db_connect.php'); 
		$r = mysql_query('SELECT id, name FROM categories ORDER BY id'); 
		$list = array(0 => array(
			'value' => 0,
			'desc' => '---SELECT---',
			'selected' => false		
		)); 
		while($row = mysql_fetch_assoc($r)) 
		{ 
			// add the next item 
			$list[] = array( 
				'value' => $row['id'], 
				'desc' => $row['name'],
				'selected' => false
			);
		}

		if(isset($_GET['selected']))
		{
			$tpl -> assign('selected', $_GET['selected']);
			if($_GET['selected'] == 0)
			{
				$tpl -> assign('message', 'Invalid choice!');
			}
		}
		else
		{
			$tpl -> assign('selected', 0);
		}

		$tpl -> assign('list', $list);

		$tpl -> parse('example7.tpl');
		mysql_close();
	}catch(optException $exception){ 
		optErrorHandler($exception); 
	}
?>
