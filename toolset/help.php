<?php

	require('./common.php');

	try
	{
		toolsetInit();

		$view -> setTitle('Help');
		$view -> setTemplate('help.tpl');

		$result = array();
		$i = 0;
		foreach($availableDirectives as $name => $values)
		{
			$result[$i] = $values;
			$result[$i]['name'] = $name;
			$i++;
		}
		$tpl -> assign('directives', $result);

		$view -> display();
	}
	catch(optException $exception)
	{
		toolsetException($exception);
	}
?>
