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
    	
    	$view = new Opt_View('test_selector_2.tpl');
		$view->s1 = array(0 =>
			array('ytem' => 'foo', 'value' => 'Some value 1'),
			array('ytem' => 'bar', 'value' => 'Some value 2'),
			array('ytem' => 'foo', 'value' => 'Some value 3'),
			array('ytem' => 'bar', 'value' => 'Some value 4'),
			array('ytem' => 'bar', 'value' => 'Some value 5'),
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