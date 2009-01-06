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
		
		$view = new Opt_View('test_content_1.tpl');
		$view->assign('existingBlock', 'Some value...');
		$view->assign('htmlContent', 'This is some text with <strong>html</strong>.');

		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>