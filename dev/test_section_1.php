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
    	$tpl->gzipCompression = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_section_1.tpl');
    	
		$section1 = array(0 =>
			array('name' => 'Joe', 'surname' => 'Smith'),
			array('name' => 'Adam', 'surname' => 'Brown'),
			array('name' => 'Mike', 'surname' => 'Oldfield'),
			array('name' => 'Stephen', 'surname' => 'King'),
			array('name' => 'Jay', 'surname' => 'Newcome')		
		);
		
		$section2 = array(
			0 => 'Webmaster', 'Programmer', 'Director', 'Boss'		
		);
		$section4 = array(
			0 => array('block' => array('subitem' => 'Test 1')),
			array('block' => array('subitem' => 'Test 2')),
			array('block' => array('subitem' => 'Test 3'))		
		);
		$view->assign('s1', $section1);
		$view->assign('s2', $section2);
		$view->assign('s4', $section4);
		
		$out = new Opt_Output_Http;
		$out->setContentType(Opt_Output_Http::HTML);
		$out->render($view); 
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>
