<?php
	require('./init.php');

	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->prologRequired = false;
		$tpl->singleRootNode = false;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->setup();
		
		$view = new Opt_View('test_inherit_10.tpl');
		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>