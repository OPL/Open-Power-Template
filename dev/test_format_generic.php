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
    	
    	$view = new Opt_View('test_format_2.tpl');
    	$view->currentFormat = 'generic';

    	$view->sect1 = array(0 =>
    		array('val' => '1'),
    		array('val' => '2'),
    		array('val' => '3'),
    		array('val' => '4')    	
    	);
    	$view->sect2 = array(0 =>
    		array(0 =>
    			array('val' => '1.1'),
    			array('val' => '1.2'),
    			array('val' => '1.3')    		
    		),
    		array(),
    		array(0 =>
    			array('val' => '3.1'),
    			array('val' => '3.2'),
    			array('val' => '3.3')    		
    		),
    		array(0 =>
    			array('val' => '4.1'),
    			array('val' => '4.2'),
    			array('val' => '4.3')    		
    		)
    	);
    	$view->sect3 = array(0 =>
    		array(0 =>
    			array(0 =>
    				array('val' => '1.1.1'),
    				array('val' => '1.1.2'),
    				array('val' => '1.1.3'),
    			),
    			array(0 =>
    				array('val' => '1.2.1'),
    				array('val' => '1.2.2'),
    				array('val' => '1.2.3'),
    			),
    			array()
    		),
    		array(),
    		array(0 =>
    			array(0 =>
    				array('val' => '3.1.1'),
    				array('val' => '3.1.2'),
    				array('val' => '3.1.3'),
    			),
    			array(0 =>
    				array('val' => '3.2.1'),
    				array('val' => '3.2.2'),
    				array('val' => '3.2.3'),
    			),
    			array()
    		),
    		array(),
    	);

    	$out = new Opt_Output_Http;
    	$out->render($view);
    }
    catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>
