<?php
	require('./init.php');
    
    class myBlock implements Opt_Block_Interface
    {
		private $_view;

    	public function setView(Opt_View $view)
    	{
    		$this->_view = $view;
    	} // end setOptInstance();
    	
		public function onOpen(Array $attributes)
		{
			echo '<div>Hey! Here are my attributes: ';
			Opl_Debug::dump($attributes);		
		} // end onOpen();

		public function onClose()
		{
			echo '</div>';
		}

		public function onSingle(Array $attributes)
		{
			echo '<div>Hey! Here are my attributes: ';
			Opl_Debug::dump($attributes);
			echo '</div>';
		} // end onSingle();
    } // end myBlock;
    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
    	
    	$view = new Opt_View('test_blocks_1.tpl');
    	$view->block = new myBlock;
    	
    	$httpOutput = new Opt_Output_Http;
    	$httpOutput->setContentType(Opt_Output_Http::HTML);
    	$httpOutput->render($view);
    }
    catch(Opt_Exception $exception)
    {
    	Opt_Error_Handler($exception);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }
?>