<?php
	require('./init.php');
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_functions_1.tpl');
		$view->assign('bigText', "This is a simple, dummy text that is going\r\nto be indented. Foo bar joe foo bar\r\njoe. Foo bar...");
		$view->assign('smallText', 'This is a simple, small text.');
		$view->assign('stupidText', 'This   is 	a 	simple			stupid, text.');
		$view->assign('truncText', 'This text is going to be truncated. Be prepared that you won\'t see some of those words in the template.');
		$view->assign('wrapText', 'This text is used to test the wrapping. I hope it will be quite useful, especially that it contains veeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeery long word.');
		$y = rand(0, 10);
		if($y > 8)
		{
			$view->assign('block1', 'Yo!');
		}
		if($y > 5)
		{
			$view->assign('block2', 'Hello!');
		}
		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>