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
		
		$view = new Opt_View('test_foreach_1.tpl');
		
		$view->data = array(
			'name' => 'John',
			'surname' => 'Smith',
			'age' => '34'		
		);
		
		$view->data2 = array();

		
		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view); 
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>