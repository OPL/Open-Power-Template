<?php

	require('./common.php');

	try
	{
		toolsetInit();

		$view -> setTitle('Index');
		$view -> setTemplate('index.tpl');
		$view -> display();
	}
	catch(optException $exception)
	{
		toolsetException($exception);
	}
?>
