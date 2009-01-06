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
		
		$view = new Opt_View('test_put_1.tpl');
		$view->s1 = array(0 =>
			array('name' => 'Joe', 'surname' => 'Smith'),
			array('name' => 'Adam', 'surname' => 'Brown'),
			array('name' => 'Mike', 'surname' => 'Oldfield'),
			array('name' => 'Stephen', 'surname' => 'King'),
			array('name' => 'Jay', 'surname' => 'Newcome')		
		);
		$view->item = 'sunglasses';

		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view); 
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>