<?php
	require('./init.php');

	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->printComments = true;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		require('./components.php');
		$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:select', 'selectComponent');
		$tpl->setup();
		$hello1 = (isset($_POST['hello1']) ? $_POST['hello1'] : 0);
		$hello2 = (isset($_POST['hello2']) ? $_POST['hello2'] : 0);
		
		$view = new Opt_View('test_components_2.tpl');
		$view->assign('list', array(0 => 
			'Position 1',
			'Position 2',
			'Position 3',
			'Position 4',
			'Position 5',
			'Position 6',
			'Position 7',
			'Position 8',
			'Position 9'		
		));
		$view->assign('selected1', $hello1);
		$view->assign('valid1', true);
		if($hello1 == 4)
		{
			$view->assign('valid1', false);
		}
		$view->assign('selected2', $hello2);
		$view->assign('valid2', true);
		if($hello2 == 4)
		{
			$view->assign('valid2', false);
		}
		
		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>