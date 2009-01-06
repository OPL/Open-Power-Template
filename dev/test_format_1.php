<?php
	require('./init.php');
    
    class fake
    {
    	private $_fields;
    	
    	public function __construct($fields)
    	{
    		$this->_fields = $fields;
    	} // end __construct();
    	
    	public function __get($name)
    	{
    		return $this->_fields[$name];
    	} // end __get();    
    } // end fake;
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_format_1.tpl');
    	
    	// Generic
    	$view->assign('generic1', 'block1 value');
    	$view->assign('generic2', array(
    		'item1' => 'generic2.item1 value',
    		'item2' => array(
    			'subitem' => 'generic2.item2.subitem value'
    		)	
    	));

    	$view->assignGlobal('generic1', 'block1 value');
    	$view->assignGlobal('generic2', array(
    		'item1' => 'generic2.item1 value',
    		'item2' => array(
    			'subitem' => 'generic2.item2.subitem value'
    		)
    	));
    	
    	// Objective
    	$view->assign('objective1', 'block1 value');
    	$view->assign('objective2', new fake(array(
    		'item1' => 'objective2.item1 value',
    		'item2' => new fake(array(
    			'subitem' => 'generic2.item2.subitem value'
    		))
    	)));

    	$view->assignGlobal('objective1', 'block1 value');
    	$view->assignGlobal('objective2', new fake(array(
    		'item1' => 'objective2.item1 value',
    		'item2' => new fake(array(
    			'subitem' => 'objective2.item2.subitem value'
    		))	
    	)));
    	$view->setFormat('objective1', 'Objective');
    	$view->setFormat('objective2', 'Objective');
    	$view->setFormat('objective2.item2', 'Objective');
    	
    	// Mixed
    	// Objective
    	$view->assign('mixed1', 'block1 value');
    	$view->assign('mixed2', new fake(array(
    		'item1' => 'mixed2.item1 value',
    		'item2' => array(
    			'subitem' => 'mixed2.item2.subitem value'
    		)
    	)));

    	$view->assignGlobal('mixed1', 'block1 value');
    	$view->assignGlobal('mixed2', new fake(array(
    		'item1' => 'mixed2.item1 value',
    		'item2' => array(
    			'subitem' => 'mixed2.item2.subitem value'
    		)
    	)));
    	$view->setFormat('mixed1', 'Objective');
    	$view->setFormat('mixed2', 'Objective');
    	
    	$out = new Opt_Output_Http;
    	$out->setContentType(Opt_Output_Http::HTML);
    	$out->render($view);    
    }
    catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>
