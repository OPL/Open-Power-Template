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
    	
    	$view = new Opt_View('test_section_3.tpl');
    	
    	$view->sect1 = array(0 =>
			array('value' => ':sect1-value1:'),
			array('value' => ':sect1-value2:')
		);
		$view->sect2 = array(0 =>
			array('value' => ':sect2-value1:'),
			array('value' => ':sect2-value2:')
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
