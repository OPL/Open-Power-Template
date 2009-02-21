<?php
	require('./init.php');

	class translationInterface implements Opl_Translation_Interface
	{
		private $_original = array(
			'foo' => array('bar' => 'Value 1', 'joe' => 'Value 2'),
			'goo' => array('bar' => 'Modificable value %s'),
		);

		private $_modified = array();

		public function _($group, $id)
		{
			if(isset($this->_modified[$group][$id]))
			{
				return $this->_modified[$group][$id];
			}
			if(isset($this->_original[$group][$id]))
			{
				return $this->_original[$group][$id];
			}
			return '';
		} // end _();
		public function assign($group, $id)
		{
			$args = func_get_args();
			unset($args[0]);
			unset($args[1]);
			if(isset($this->_original[$group][$id]))
			{
				if(!isset($this->_modified[$group]))
				{
					$this->_modified[$group] = array();
				}
				$this->_modified[$group][$id] = vsprintf($this->_original[$group][$id], $args);
			}
		} // end assign();
	} // end translationInterface;

    
    try
    {
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();
		$tpl->setTranslationInterface(new translationInterface());
    	
    	$view = new Opt_View('test_translation_1.tpl');
    	$view->foo = 'A foo value';
    	
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