<?php
/*
 * XML TEST
 * ------------------------------------
 * This test checks how the XML files are parsed by OPT.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('XML_DIR', './xml/');
		define('CPL_DIR', './templates_c/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		require('./includes/filesystemWrapper.php');
	}

	class xmlTest extends PHPUnit_Framework_TestCase
	{
	    protected $tpl;
	    protected $dataGenerators = array();

	    protected function setUp()
	    {
			$tpl = new Opt_Class;
			$tpl->sourceDir = 'test://templates/';
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

	    public static function correctProvider()
	    {
	    	return array(0 =>
	    		array('tags_1.txt'),
				array('tags_2.txt'),
				array('tags_3.txt'),
				array('tags_4.txt'),
				array('tags_5.txt'),
				array('tags_6.txt'),
				array('tags_7.txt'),
				array('prolog_1.txt'),
				array('prolog_2.txt'),
				array('prolog_3.txt'),
				array('prolog_4.txt'),
				array('dtd_1.txt'),
				array('dtd_2.txt'),
				array('dtd_3.txt'),
	    	);
	    } // end correctProvider();

	    private function stripWs($text)
	    {
	    	return str_replace(array("\r", "\n"),array('', ''), $text);
	    } // end stripws();

 	   /**
 	    * @dataProvider correctProvider
 	    */
	    public function testCorrect($test)
	    {
			testFSWrapper::loadFilesystem(XML_DIR.$test);
	    	$view = new Opt_View('test.tpl');
			if(file_exists('test://data.php'))
			{
				eval(file_get_contents('test://data.php'));
			}

			$out = new Opt_Output_Return;
			$expected = file_get_contents('test://expected.txt');

			if(strpos($expected, 'OUTPUT') === 0)
			{
				// This test shoud give correct results
	    		try
	    		{
					$result = $out->render($view);
	    			$this->assertEquals($this->stripWs(trim(file_get_contents('test://result.txt'))), $this->stripWs(trim($result)));
	    		}
	    		catch(Opt_Exception $e)
	    		{
	    			$this->fail('Exception returned: #'.get_class($e).': '.$e->getMessage());
	    		}
			}
			else
			{
				// This test should generate an exception
				$expected = trim($expected);
				try
				{
					$out->render($view);
				}
				catch(Opt_Exception $e)
				{
	    			if($expected != get_class($e))
	    			{
	    				$this->fail('Invalid exception returned: #'.get_class($e).', '.$expected.' expected.');
	    			}
	    			return true;
				}
				$this->fail('Exception NOT returned, but should be: '.$expected);
			}
	    } // end testCorrect();
	}
?>

