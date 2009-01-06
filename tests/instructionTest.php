<?php
/*
 * INSTRUCTION TEST
 * ------------------------------------
 * This test checks, whether the instructions work, as they should work, by checking the generated content.
 * The procedure does not contain template inheritance issues, because they have a separate procedure.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('INS_DIR', './instruction/');
		define('CPL_DIR', './templates_c/');
		define('RES_DIR', './results/');
		define('DAT_DIR', './data/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	}
	
	class testFSWrapper
	{
		static private $files;
		
		private $file;
		private $read;
		
		static public function loadFilesystem($fs)
		{
			self::$files = array();
			$lines = file($fs);
			$currentFile = null;
			foreach($lines as $line)
			{
				if(strpos($line, '>>>>') === 0)
				{
					$currentFile = 'test://'.trim(substr($line, 4, strlen($line)));
					self::$files[$currentFile] = '';
					continue;
				}
				
				if(!is_null($currentFile))
				{
					self::$files[$currentFile] .= $line;
				}
			}
		} // end loadFilesystem();
		
		public function stream_open($path, $mode, $options, $opened_path)
		{		
			$this->file = $path;
			if(!isset(self::$files[$path]))
			{
				return false;
			}
			return true;
		} // end stream_open();
	
		public function stream_close()
		{		
			
		} // end stream_close();
		
		public function stream_eof()
		{
			return true;
			return ($this->read >= strlen(self::$files[$this->file]));
		} // end stream_eof();
		
		public function stream_read($count)
		{		
			$return = substr(self::$files[$this->file], $this->read, $count);
			$this->read += $count;
			return $return;
		} // end stream_read();
		
		public function stream_write($data)
		{		
		
		} // end stream_write();
		
		public function stream_tell()
		{		
			return $this->read;
		} // end stream_tell();
		
		public function stream_seek($offset, $whence)
		{		
		
		} // end stream_seek();
		
		public function stream_stat()
		{		
			if(!isset(self::$files[$this->file]))
			{
				return array();
			}
			return array('size' => strlen($this->file));
		} // end stream_stat();
		
		public function url_stat($path, $flags)
		{		
			if(!isset(self::$files[$path]))
			{
				return false;
			}
			return array('size' => strlen($path));
		} // end url_stat();
	} // end testFSWrapper;
	
	stream_register_wrapper('test', 'testFSWrapper');
	
	class instructionTest extends PHPUnit_Framework_TestCase
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
	    		array('extend_1.txt'),
	    		array('extend_2.txt'),
	    		array('extend_3.txt'),
	    		array('extend_4.txt'),
	    		array('extend_5.txt'),
	    		array('extend_6.txt'),
	    		array('extend_7.txt'),
	    		array('extend_8.txt'),
	    		array('extend_9.txt'),
	    		array('extend_10.txt'),
	    		array('extend_11.txt'),
	    		array('for_1.txt'),
	    		array('for_2.txt'),
	    		array('for_3.txt'),
	    		array('foreach_1.txt'),
	    		array('foreach_2.txt'),
	    		array('foreach_3.txt'),
	    		array('foreach_4.txt'),
	    		array('foreach_5.txt'),
	    		array('foreach_6.txt'),
	    		array('foreach_7.txt'),
	    		array('foreach_8.txt'),
	    		array('foreach_9.txt'),
	    		array('foreach_10.txt'),
	    		array('if_1.txt'),
	    		array('if_2.txt'),
	    		array('if_3.txt'),
	    		array('if_4.txt'),
	    		array('if_5.txt'),
	    		array('if_6.txt'),
	    		array('if_7.txt'),
	    		array('if_8.txt'),
	    		array('if_9.txt'),
	    		array('if_10.txt'),
	    		array('include_1.txt'),
	    		array('include_2.txt'),
	    		array('include_3.txt'),
	    		array('include_4.txt'),
	    		array('include_5.txt'),
	    		array('include_6.txt'),
	    		array('include_7.txt'),
	    		array('insert_1.txt'),
	    		array('insert_2.txt'),
	    		array('insert_3.txt'),
	    		array('insert_4.txt'),
	    		array('insert_5.txt'),
	    		array('literal_1.txt'),
	    		array('literal_2.txt'),
	    		array('literal_3.txt'),
	    		array('literal_4.txt'),
	    		array('repeat_1.txt'),
	    		array('repeat_2.txt'),
	    		array('repeat_3.txt'),
	    		array('repeat_4.txt'),
	    		array('section_1.txt'),
	    		array('section_2.txt'),
	    		array('section_3.txt'),
	    		array('snippet_1.txt'),
	    		array('snippet_2.txt'),
	    		array('snippet_3.txt'),
	    		array('snippet_4.txt'),
	    		array('snippet_5.txt'),
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
			testFSWrapper::loadFilesystem(INS_DIR.$test);
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
