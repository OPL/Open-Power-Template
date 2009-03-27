<?php
	require('./init.php');
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_tree_2.tpl');
    	
		$view->tree = array(0 =>
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
		);
		
		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view); 
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>
