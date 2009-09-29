<?php

	function optPrefilterTest($tpl, $code)
	{
		return '-'.$code.'-';	
	} // end optPrefilterTest();

	function optPostfilterTest($tpl, $code)
	{
		return '*'.$code.'*';	
	} // end optPostfilterTest();
	
	function optOutputfilterTest($tpl, $code)
	{
		return trim($code);	
	} // end optOutputfilterTest();
	
	class optTestClass extends optClass
	{
		public $hh = '';
		
		protected function header($header)
		{
			if(strpos($header, 'Content-type') !== false)
			{
				$this -> hh = $header;
			}		
		} // end header();	
	}

	class optTest extends PHPUnit_TestCase
	{
		private $opt;
		
		public function __construct($name)
		{
			$this -> PHPUnit_TestCase($name);
		} // end __construct();
		
		public function setUp()
		{
			$this -> opt = new optTestClass;
			$this -> opt -> root = './templates/';
			$this -> opt -> compile = './templates_c/';
			$this -> opt -> gzipCompression = 0;
			$this -> opt -> compileCacheDisabled = 1;
			$this -> opt -> parseintDecPoint = ',';
			$this -> opt -> parseintThousands = ' ';		
		} // end setUp();
		
		public function tearDown()
		{
			unset($this -> opt);		
		} // end tearDown();
		
		public function testTemplateDirExists()
		{
			$this -> assertTrue(file_exists($this->opt->root));		
		} // end testTemplateDirExists();
	
		public function testTemplateDirReadable()
		{
			$this -> assertTrue(is_readable($this->opt->root));		
		} // end testTemplateDirReadable();

		public function testCompileDirExists()
		{
			$this -> assertTrue(file_exists($this->opt->compile));		
		} // end testCompileDirExists();
	
		public function testCompileDirReadable()
		{
			$this -> assertTrue(is_readable($this->opt->compile));		
		} // end testCompileDirReadable();
		
		public function testCompileDirWriteable()
		{
			$this -> assertTrue(is_writeable($this->opt->compile));		
		} // end testCompileDirWriteable();
		
		public function testCorrectNewlinesClass()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.class.php')) > 1);		
		} // end testCorrectNewlinesClass();
		
		public function testCorrectNewlinesCompiler()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.compiler.php')) > 1);		
		} // end testCorrectNewlinesCompiler();
		
		public function testCorrectNewlinesInstructions()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.instructions.php')) > 1);		
		} // end testCorrectNewlinesInstructions();
		
		public function testCorrectNewlinesFunctions()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.functions.php')) > 1);		
		} // end testCorrectNewlinesFunctions();
		
		public function testCorrectNewlinesComponents()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.components.php')) > 1);		
		} // end testCorrectNewlinesComponents();
		
		public function testCorrectNewlinesCore()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.core.php')) > 1);		
		} // end testCorrectNewlinesCore();
		
		public function testCorrectNewlinesError()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.error.php')) > 1);		
		} // end testCorrectNewlinesError();
		
		public function testCorrectNewlinesApi()
		{
			$this -> assertTrue(count(file(OPT_DIR.'opt.api.php')) > 1);		
		} // end testCorrectNewlinesApi();
		
		public function testMethodParse()
		{
			ob_start();
			$this -> opt -> parse('basic.tpl');
			$result = ob_get_contents();
			ob_end_clean();
			$this -> assertEquals('HELLO WORLD!', $result);		
		} // end testMethodParse();
		
		public function testMethodFetch()
		{
			$result = $this -> opt -> fetch('basic.tpl');
			$this -> assertEquals('HELLO WORLD!', $result);		
		} // end testMethodFetch();	
		
		public function testMethodParseCapture()
		{
			$this -> opt -> parseCapture('basic.tpl', 'basic');
			ob_start();
			$this -> opt -> parse('parseCapture.tpl');
			$result = ob_get_contents();
			ob_end_clean();
			$this -> assertEquals('HELLO WORLD!', $result);		
		} // end testMethodFetch();
		
		public function testLiteral()
		{
			$result = $this -> opt -> fetch('literal.tpl');
			$this -> assertEquals('TEXT {$variable} TEXT', $result);		
		} // end testLiteral();
		
		public function testMath()
		{
			$this -> opt -> assign('value', array(
				'a' => 5,
				'b' => 17,
				'c' => 3.14,
				'd' => -8
			));
			// A + B
			// A * C
			// A / D
			// (A - B) * C
			// C + (-9.01)
			$result = $this -> opt -> fetch('math.tpl');
			$this -> assertEquals('22 ;
15.7 ;
-0.625 ;
-37.68 ;
-5.87 ;
', $result);
		} // end testMath();

		public function testParseInt()
		{
			$this -> opt -> assign('value', array(
				'a' => 85764,
				'b' => 32767,
				'c' => 21846.567,
				'd' => 34109.22			
			));
			// A + B
			// A + C
			// A + D
			// B + C
			// C * D
			$result = $this -> opt -> fetch('parseint.tpl');
			$this -> assertEquals('118 531 ;
107 610,567 ;
119 873,22 ;
54 613,567 ;
745 169 360,048 ;
', $result);
		} // end testParseInt();
		
		public function testPrefilterRegistration()
		{
			$ok1 = $this -> opt -> registerFilter(OPT_PREFILTER, 'Test');
			$ok2 = $this -> opt -> unregisterFilter(OPT_PREFILTER, 'Test');
			$this -> assertTrue($ok1 && $ok2);		
		} // end testPrefilterRegistration();
		
		public function testPostfilterRegistration()
		{
			$ok1 = $this -> opt -> registerFilter(OPT_POSTFILTER, 'Test');
			$ok2 = $this -> opt -> unregisterFilter(OPT_POSTFILTER, 'Test');
			$this -> assertTrue($ok1 && $ok2);		
		} // end testPostfilterRegistration();
		
		public function testOutputfilterRegistration()
		{
			$ok1 = $this -> opt -> registerFilter(OPT_OUTPUTFILTER, 'Test');
			$ok2 = $this -> opt -> unregisterFilter(OPT_OUTPUTFILTER, 'Test');
			$this -> assertTrue($ok1 && $ok2);		
		} // end testOutputfilterRegistration();
		
		public function testCompilationFilters()
		{
			$this -> opt -> registerFilter(OPT_PREFILTER, 'Test');
			$this -> opt -> registerFilter(OPT_POSTFILTER, 'Test');
			$this -> assertEquals('*-  Hello World  -*', $this -> opt -> fetch('filters.tpl'));		
		} // end testCompilationFilters();
		
		public function testRuntimeFilters()
		{
			$this -> opt -> registerFilter(OPT_OUTPUTFILTER, 'Test');
			$this -> assertEquals('Hello World', $this -> opt -> fetch('outputfilters.tpl'));		
		} // end testRuntimeFilters();
		
		public function testSections()
		{
			$this -> opt -> assign('sect', array(0 =>
				array('val' => 'foo'),
				array('val' => 'bar')
			));
			$this -> opt -> assign('table', array(
				'value1' => 'abc',
				'value2' => 'def'
			));
			$result = $this -> opt -> fetch('sections.tpl');
			$this -> assertEquals('foo - abc - def
bar - abc - def
', $result);
		} // end testSections();
		
		public function testFor()
		{
			$result = $this -> opt -> fetch('for.tpl');
			$this -> assertEquals('1
2
3
4
5
6
', $result);
		} // end testFor();
		
		public function testContentTypeDetection1()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html;q=1';
			$this -> opt -> httpHeaders(OPT_XHTML);			
			$this -> assertEquals('Content-type: text/html', $this -> opt -> hh);	
		} // end testContentTypeDetection1();
		
		public function testContentTypeDetection2()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.5,application/xhtml+xml;q=0.8';
			$this -> opt -> httpHeaders(OPT_XHTML);			
			$this -> assertEquals('Content-type: application/xhtml+xml', $this -> opt -> hh);	
		} // end testContentTypeDetection2();
	
		public function testContentTypeDetection3()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.8,application/xhtml+xml;q=0.5';
			$this -> opt -> httpHeaders(OPT_XHTML);			
			$this -> assertEquals('Content-type: text/html', $this -> opt -> hh);	
		} // end testContentTypeDetection3();
		
		public function testContentTypeDetection4()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.8,application/xhtml+xml;q=0.5';
			$this -> opt -> httpHeaders(OPT_FORCED_XHTML);			
			$this -> assertEquals('Content-type: application/xhtml+xml', $this -> opt -> hh);	
		} // end testContentTypeDetection4();
		
		public function testContentTypeDetection5()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html;q=0.8';
			$this -> opt -> httpHeaders(OPT_FORCED_XHTML);			
			$this -> assertEquals('Content-type: text/html', $this -> opt -> hh);	
		} // end testContentTypeDetection5();
		
		public function testContentTypeDetection6()
		{
			$this -> opt -> httpHeaders('image/jpeg');			
			$this -> assertEquals('Content-type: image/jpeg', $this -> opt -> hh);	
		} // end testContentTypeDetection6();
		
		public function testInvalidContentType()
		{
			try
			{
				$this -> opt -> httpHeaders(17);
			}
			catch(optException $e)
			{
				if($e -> getCode() == OPT_E_CONTENT_TYPE)
				{
					return 1;
				}
			}		
			$this -> fail('Exception not returned.');	
		} // end testContentTypeDetection6();
		
		public function testApi()
		{
			require_once(OPT_DIR.'opt.api.php');
			
			$api = new optApi;
			
			foreach($this -> opt -> configDirectives as $directive)
			{
				if(!empty($api -> {$directive}) && !isset($api -> {$directive}))
				{
					$this -> fail('Configuration directive '.$directive.' not set in OPT API!');
				}				
			}
			$methodList = array(0 =>
				'error', 'assign', 'assignRef', 'assignGroup',
				'setDefaultI18n', 'setObjectI18n', 'registerInstruction',
				'registerInstructionFile', 'parse', 'fetch'				
			);
			
			foreach($methodList as $method)
			{
				if(!method_exists($api, $method))
				{
					$this -> fail('Method '.$method.' does not exist in OPT API!');
				}				
			}
			
			return 1;			
		} // end testApi();
	}

?>
