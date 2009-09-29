<?php
	define('ITEM_NUM', 300);
	define('PAGE_SIZE', '10');
	define('OPT_DIR', '../lib/');
	require(OPT_DIR.'opt.class.php');
	require('./pagesystem.php');
	
	try
	{
		$tpl = new optClass;
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> charset = 'iso-8859-2';
		$tpl -> debugConsole = true;
		$tpl -> alwaysRebuild = true;
		
		$tpl -> httpHeaders(OPT_HTML);
		$data = array();
		// Create data array
		for($i = 1; $i <= ITEM_NUM; $i++)
		{
			$data[] = 'Item '.$i;
		}

		$ps = new pagesystem(ITEM_NUM, PAGE_SIZE, 'example19.php');

		$rows = array();

		for($i = $ps -> startPos(); $i <= $ps -> endPos(); $i++)
		{
			if(isset($data[$i]))
			{
				$rows[] = array('item' => $data[$i]);
			}
			else
			{
				break;
			}
		}

		$tpl -> assign('list', $rows);
		$tpl -> assign('ps', $ps);
		$tpl -> parse('example19.tpl');
	}
	catch(optException $exception)
	{
		optErrorHandler($exception);
	}

?>
