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
    	
    	$view = new Opt_View('test_functions_2.tpl');
    	
		$view->assign('fooText', 'This is a text with foo.');
		$view->assign('array', array('foo', 'bar'));
		$view->assign('url1', 'http://www.invenzzia.org/index');
		$view->assign('url2', 'foo bar joe');
		
		$view->assign('img1', 'http://www.example.com/foo/image.jpg');
		$view->assign('img2', 'http://www.example.com/foo/image.html');
		
		$view->assign('numbers', array(0 =>
			5, 9, 23, 1, 4, 8, 6, 7, 11, 14, 6, 12, 4, 6	
		));
		$view->assign('texts', new ArrayIterator(Array(
			'foo' => 'This is a text',
			'bar' => 'packed into an object',
			'joe' => 'which is correctly processed.'
		)));
		$view->setFormat('texts', 'Objective');
		
		setlocale(LC_MONETARY, 'en_US');
		
		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>