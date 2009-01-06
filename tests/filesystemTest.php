<?php
/*
 * API TEST
 * ------------------------------------
 * The goal of this test is to check that all the API is correctly named and set. This is especially useful
 * while making new releases, when we have to be sure that the function and field names are not changed
 * by mistake. 
 * 
 * The testing procedure also checks the OPT manual.
 */
	require_once('PHPUnit/Framework.php');

	define('M_PUBLIC', 0);
	define('M_PROTECTED', 1);

	if(!defined('GROUPED'))
	{
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}

	class filesystemTest extends PHPUnit_Framework_TestCase
	{
	    protected $tpl;

	    protected function setUp()
	    {
			$this->tpl = new Opt_Class;
			$this->tpl->sourceDir = './templates';
			$this->tpl->compileDir = './templates_c';
	    } // end setUp();
	 
	    protected function tearDown()
	    {
	        $this->tpl = NULL;
	    } // end tearDown();
		
	    public function testSlashing()
	    {
	    	$this->tpl->setup();
	    	
	    	if($this->tpl->sourceDir['file'] != './templates/' || $this->tpl->compileDir != './templates_c/')
	    	{
	    		$this->fail('No ending slash in the paths. SourceDir: '.$this->tpl->sourceDir.'; CompileDir: '.$this->tpl->compileDir);
	    	}
	    } // end testSlashing();
	    
	    public function testInvalidTemplate()
	    {
	    	try
	    	{
	    		$view = new Opt_View('template_that_doesnt_exist.tpl');
	    		$out = new Opt_Output_Return;
	    		$out->render($view);
	    	}
	    	catch(Opt_TemplateNotFound_Exception $exception)
	    	{
	    		return true;
	    	}
	    	$this->fail('Opt_TemplateNotFound_Exception not returned.');
	    } // end testInvalidTemplate();
	} // end expressionTestSuite;
?>
