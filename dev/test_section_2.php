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
    	
    	$view = new Opt_View('test_section_2.tpl');
    	
    	$view->assign('foo', array(0 =>
    		array('block' => 'abc'),
    		array('block' => 'def'),
    		array('block' => 'ghi'),
    		array('block' => 'jkl')
    	));
    	$view->assign('bar', array(0 =>
    		array(0 =>
	    		array('block' => 'abc'),
	    		array('block' => 'def'),
	    		array('block' => 'ghi')
    		),
    		array(0 =>
	    		array('block' => 'abc'),
	    		array('block' => 'def'),
	    		array('block' => 'ghi')
    		),
    		array(0 =>
	    		array('block' => 'abc'),
	    		array('block' => 'def'),
	    		array('block' => 'ghi')
    		)
    	));
    	$out = new Opt_Output_Http;
    	$out->setContentType(Opt_Output_Http::HTML);
    	$out->render($view);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>
