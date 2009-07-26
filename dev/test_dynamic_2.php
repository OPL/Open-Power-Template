<?php
	require('./init.php');

	class testCache implements Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view)
		{
			ob_start();
			$tpl = Opl_Registry::get('opt');
			$tpl->setBufferState('cache', true);
			return false;
		} // end templateCacheStart();

		public function templateCacheStop(Opt_View $view)
		{
			$tpl = Opl_Registry::get('opt');

			if($view->hasDynamicContent())
			{
				$buffers = $view->getOutputBuffers();
				$buffers[] = ob_get_flush();
				var_dump($view->getOutputBuffers());
			}

			$tpl->setBufferState('cache', false);
		} // end templateCacheStop();
	} // end testCache;

	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->prologRequired = true;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->setup();

		$view = new Opt_View('test_dynamic_1.tpl');
		$view->setCache(new testCache);
		$view->lol = 'Lol';

		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>