<?php
	require('./init.php');
	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->prologRequired = true;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->setup();
		
		$out = new Opt_Output_Http;
		$out->render(new Opt_View('test_dtd_2.tpl'));
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>