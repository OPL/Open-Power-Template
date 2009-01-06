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
    	
    	$view = new Opt_View('test_cycle_1.tpl');
    	
		$view->data = array(0 =>
			array('name' => 'Joe', 'surname' => 'Smith'),
			array('name' => 'Adam', 'surname' => 'Brown'),
			array('name' => 'Mike', 'surname' => 'Oldfield'),
			array('name' => 'Stephen', 'surname' => 'King'),
			array('name' => 'Jay', 'surname' => 'Newcome')		
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