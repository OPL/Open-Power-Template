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
    	
    	$view = new Opt_View('test_parser_1.tpl');
    	$view->foo = 'A foo value';

		$x = $view->foo;
		$y = $view->bar;
    	
    	$httpOutput = new Opt_Output_Http;
    	$httpOutput->setContentType(Opt_Output_Http::HTML);
    	$httpOutput->render($view);
    }
    catch(Opt_Exception $exception)
    {
    	Opt_Error_Handler($exception);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>