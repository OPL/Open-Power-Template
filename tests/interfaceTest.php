<?php
/*
 * INTERFACE TEST
 * ------------------------------------
 * This unit test suite checks the features provided by the API that cannot
 * be checked in the other way or do not belong to any other category.
 */
	require_once('PHPUnit/Framework.php');

	define('M_PUBLIC', 1);
	define('M_PROTECTED', 2);
	define('M_STATIC', 4);
	define('M_FINAL', 8);
	define('M_ABSTRACT', 16);

	if(!defined('GROUPED'))
	{
		define('INS_DIR', './instruction/');
		define('CPL_DIR', './templates_c/');
		define('RES_DIR', './results/');
		define('DAT_DIR', './data/');
		define('DOC_DIR', '../docs/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}

	class inactiveCache implements Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view)
		{
			echo 'CACHE-CHECK-START ';
			return false;
		} // end templateCacheStart();

		public function templateCacheStop(Opt_View $view)
		{
			echo ' CACHE-CHECK-STOP';
		} // end templateCacheStop();
	} // end inactiveCache;

	class activeCache implements Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view)
		{
			echo 'CACHE-CHECK-START ';
			return true;
		} // end templateCacheStart();

		public function templateCacheStop(Opt_View $view)
		{
			echo ' CACHE-CHECK-STOP';
		} // end templateCacheStop();
	} // end activeCache;

	class interfaceTest extends PHPUnit_Framework_TestCase
	{

	    private function stripWs($text)
	    {
	    	return trim(str_replace(array("\r", "\n"),array('', ''), $text));
	    } // end stripws();

	    protected function setUp()
	    {
			$tpl = new Opt_Class;
			$tpl->sourceDir = './interface/';
			$tpl->compileDir = CPL_DIR;
			$tpl->compileMode = Opt_Class::CM_REBUILD;
			$tpl->stripWhitespaces = false;
			$tpl->prologRequired = true;
			$tpl->setup();
			$this->tpl = $tpl;
	    } // end setUp();

	    protected function tearDown()
	    {
	        unset($this->tpl);
	    } // end tearDown();

		/**
		 * Does the engine add the slashes to the paths?
		 */
	    public function testSlashing()
	    {
	    	$foo = new Opt_Class;
			$foo->sourceDir = './templates';
			$foo->compileDir = './templates_c';
			$foo->setup();

	    	if($foo->sourceDir['file'] != './templates/' || $foo->compileDir != './templates_c/')
	    	{
	    		$this->fail('No ending slash in the paths. SourceDir: '.$foo->sourceDir.'; CompileDir: '.$foo->compileDir);
	    	}

			unset($foo);
	    } // end testSlashing();

		/**
		 * Check if the Return output actually returns the output.
		 */
		public function testReturnOutput()
		{
			$view = new Opt_View('sample.tpl');

			$output = new Opt_Output_Return;
			$this->assertEquals('ORIGINAL', $this->stripWs($output->render($view)));
		} // end testReturnOutput();

		/**
		 * Check if the Return output requests to throw the exceptions.
		 */
		public function testReturnOutputException()
		{
			$view = new Opt_View('fake.tpl');

			try
			{
				$output = new Opt_Output_Return;
				$output->render($view);
			}
			catch(Opt_TemplateNotFound_Exception $exception)
			{
				return true;
			}
			$this->fail('Opt_TemplateNotFound_Exception not returned');
		} // end testReturnOutputException();

		/**
		 * Check if the Http output actually sends the output to the browser
		 * and if the output is buffered.
		 */
		public function testHttpOutput()
		{
			$view = new Opt_View('sample.tpl');

			$output = new Opt_Output_Http;
			$output->render($view);
			$this->assertEquals('ORIGINAL', $this->stripWs(ob_get_clean()));
		} // end testHttpOutput();

		/**
		 * Test the lock of the HTTP output.
		 */
		public function testHttpOutputLock()
		{
			$view = new Opt_View('sample.tpl');

			try
			{
				$output = new Opt_Output_Http;
				ob_start();
				$output->render($view);
				$output->render($view);
				ob_end_clean();
				ob_end_clean();
			}
			catch(Opt_OutputOverloaded_Exception $exception)
			{
				ob_end_clean();
				ob_end_clean();
				return true;
			}
			@ob_end_clean();
			@ob_end_clean();
			$this->fail('Opt_OutputOverloaded_Exception not returned');
		} // end testHttpOutput();

		/**
		 * Check if the Http output requests to throw the exceptions.
		 */
		public function testHttpOutputException()
		{
			$view = new Opt_View('fake.tpl');

			try
			{
				$output = new Opt_Output_Http;
				$output->render($view);
			}
			catch(Opt_TemplateNotFound_Exception $exception)
			{
				return true;
			}
			$this->fail('Exception not returned for the template that does not exist.');
		} // end testReturnOutputException();

		/**
		 * Check if the template execution works, if there is
		 * no cache in the view.
		 */
		public function testCacheInactive()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache();

			$output = new Opt_Output_Return;
			$this->assertEquals('ORIGINAL', trim($output->render($view)));
		} // end testCacheInactive();

		/**
		 * The original template must also be executed, if there is
		 * a caching subsystem, but it decides that the template must be recompiled.
		 */
		public function testCacheProvidedButNotUsed()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache(new inactiveCache);

			$output = new Opt_Output_Return;
			$this->assertEquals('CACHE-CHECK-START ORIGINAL CACHE-CHECK-STOP', $this->stripWs($output->render($view)));
		} // end testCacheProvidedButNotUsed();

		/**
		 * In this case, the result is generated directly by the caching engine, so
		 * the original template should not be displayed.
		 */
		public function testCacheProvidedAndUsed()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache(new activeCache);

			$output = new Opt_Output_Return;
			$this->assertEquals('CACHE-CHECK-START', $this->stripWs($output->render($view)));
		} // end testCacheProvidedAndUsed();
	} // end interfaceTest;