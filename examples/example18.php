<?php

	define('OPT_DIR', '../lib/');
	require(OPT_DIR.'opt.class.php');
	
	try
	{
		$tpl = new optClass;
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> charset = 'iso-8859-2';
		$tpl -> debugConsole = true;
		$tpl -> alwaysRebuild = true;
		
		$tpl -> httpHeaders(OPT_HTML);

		$tpl -> assign('mytree', array(0 =>
			array('title' => 'Main category 1', 'depth' => 0),
			array('title' => 'Main category 2', 'depth' => 0),
			array('title' => 'Subcategory 2.1', 'depth' => 1),
			array('title' => 'Main category 3', 'depth' => 0),
			array('title' => 'Subcategory 3.1', 'depth' => 1),
			array('title' => 'Item 3.1.1', 'depth' => 2),
			array('title' => 'Item 3.1.2', 'depth' => 2),
			array('title' => 'Item 3.1.3', 'depth' => 2),
			array('title' => 'Subcategory 3.2', 'depth' => 1),
			array('title' => 'Subcategory 3.3', 'depth' => 1),
			array('title' => 'Item 3.3.1', 'depth' => 2),
			array('title' => 'Main category 4', 'depth' => 0),
			array('title' => 'Subcategory 4.1', 'depth' => 1),
			array('title' => 'Item 4.1.1', 'depth' => 2)
		));

		$tpl -> parse('example18.tpl');
	}
	catch(optException $exception)
	{
		optErrorHandler($exception);
	}

?>
