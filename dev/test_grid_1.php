<?php
	require('./init.php');
    
    try
    {
		register_tick_function("profile");
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_grid_1.tpl');
    	
		$view->s1 = array(0 =>
			array('item' => '1'),
			array('item' => '2'),
			array('item' => '3'),
			array('item' => '4'),
			array('item' => '5'),
			array('item' => '6'),
			array('item' => '7'),
			array('item' => '8'),
			array('item' => '9'),
			array('item' => '10'),
			array('item' => '11'),
			array('item' => '12'),
			array('item' => '13'),
			array('item' => '14'),
			array('item' => '15'),
		);

    	$out = new Opt_Output_Http;
    	$out->setContentType(Opt_Output_Http::HTML);
    	$out->render($view);  
    }
    catch(Opt_Exception $exception)
    {
    	Opt_Error_Handler($exception);
    }
?>
