<?php
	require('./init.php');
   
    class ObjItem implements Countable
    {
    	private $data;

    	public function __construct($data)
    	{
    		$this->data = $data;    		
    	} // end __construct();
    	
    	public function __get($name)
    	{
    		if(isset($this->data[$name]))
    		{
    			return $this->data[$name];
    		}
    		return NULL;
    	} // end __get();
    	
    	public function count()
    	{
    		return sizeof($this->data);
    	} // end count();
    } // end ObjItem;
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->compileId = 'objective';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_format_2.tpl');
    	$view->currentFormat = 'Objective';

    	$view->sect1 = new ArrayIterator(array(0 =>
    		new ObjItem(array(
    			'val' => '1',
    			'sect2' => 
	    		new ArrayIterator(array(0 =>
	    			new ObjItem(array('val' => '1.1', 'sect3' => new ArrayIterator(array(0 =>
	    				new ObjItem(array('val' => '1.1.1')),
	    				new ObjItem(array('val' => '1.1.2')),
	    				new ObjItem(array('val' => '1.1.3')),
	    			)))),
	    			new ObjItem(array('val' => '1.2', 'sect3' => new ArrayIterator(array(0 =>
	    				new ObjItem(array('val' => '1.2.1')),
	    				new ObjItem(array('val' => '1.2.2')),
	    				new ObjItem(array('val' => '1.2.3')),
	    			)),
    				)),
	    			new ObjItem(array('val' => '1.3'))    		
	    		))
    		)),
    		new ObjItem(array('val' => '2')),
    		new ObjItem(array('val' => '3', 'sect2' => 
	    		new ArrayIterator(array(0 =>
	    			new ObjItem(array('val' => '3.1', 'sect3' =>
	    				new ArrayIterator(array(0 =>
	    					new ObjItem(array('val' => '3.1.1')),
	    					new ObjItem(array('val' => '3.1.2')),
	    					new ObjItem(array('val' => '3.1.3')),
	    				)),
	    			)),
	    			new ObjItem(array('val' => '3.2', 'sect3' => new ArrayIterator(array(0 =>
	    				new ObjItem(array('val' => '3.2.1')),
	    				new ObjItem(array('val' => '3.2.2')),
	    				new ObjItem(array('val' => '3.2.3')),
	    			)),
    				)),
	    			new ObjItem(array('val' => '3.3'))    		
	    		)),
    		)),
    		new ObjItem(array('val' => '4', 'sect2' => 
	    		new ArrayIterator(array(0 =>
	    			new ObjItem(array('val' => '4.1')),
	    			new ObjItem(array('val' => '4.2')),
	    			new ObjItem(array('val' => '4.3'))    		
	    		))
    		))
    	));
    	$view->setFormat('sect1', 'Objective');
		$view->setFormat('sect2', 'Objective');
		$view->setFormat('sect3', 'Objective');
    	$out = new Opt_Output_Http;
    	$out->render($view);
    }
    catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>
