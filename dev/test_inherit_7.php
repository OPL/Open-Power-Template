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
		
		if(!isset($_GET['module']))
		{
			$view = new Opt_View('test_inherit_7a.tpl');
		
			$view->assign('modules', array(0 => 
				array('view' => new Opt_View('test_inherit_7b.tpl'))
			));
		}
		else
		{
			$view = new Opt_View('test_inherit_7b.tpl');	
		
			$view->assign('modules', array(0 => 
				array('view' => new Opt_View('test_inherit_7a.tpl'))	
			));
		}
		
		$out = new Opt_Output_Http;
		$out->render($view);
		
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>