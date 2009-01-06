<?php
	require('./init.php');
	
	function myHandler($text)
	{
		return strtoupper($text);
	} // end myHandler();

	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->prologRequired = true;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->backticks = 'myHandler';
		$tpl->setup();
		
		$view = new Opt_View('test_backticks_1.tpl');
		$view->assign('rand', rand(0,1));

		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view); 
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>