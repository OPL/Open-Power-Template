<?php
	require('./init.php');
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->mode = Opt_Class::QUIRKS_MODE;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_quirks_1.tpl');
		$view->s1 = array(0 =>
			array('name' => 'Joe', 'surname' => 'Smith'),
			array('name' => 'Adam', 'surname' => 'Brown'),
			array('name' => 'Mike', 'surname' => 'Oldfield'),
			array('name' => 'Stephen', 'surname' => 'King'),
			array('name' => 'Jay', 'surname' => 'Newcome')		
		);

		$view->style = 'style="foo"';

		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view);    
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>
