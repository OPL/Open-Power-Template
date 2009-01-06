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
		$sNumber = (isset($_POST['number']) ? $_POST['number'] : 1);

		$numbers = array(1 => '1', '2', '3', '4', '5');
		
		$view = new Opt_View('test_components_3.tpl');
		$view->assign('numbers', $numbers);
		$view->assign('sNumber', $sNumber);

		$list = array(0 => 
			'Position 1',
			'Position 2',
			'Position 3',
			'Position 4',
			'Position 5',
			'Position 6',
			'Position 7',
			'Position 8',
			'Position 9'
		);
		
		$items = array();
		for($i = 1; $i <= $sNumber; $i++)
		{
			$v = (isset($_POST['n'.$i]) ? $_POST['n'.$i] : 0);
			
			$component = new selectComponent();
			$component->set('name', 'n'.$i);
			$component->set('selected', $v);
			$component->set('valid', ($v == 4 ? false : true));
			$component->set('title', 'List '.$i);
			$component->setDatasource($list);
			$items[] = array('component' => $component);
		}
		$view->assign('items', $items);		
		
		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>