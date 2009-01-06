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
		define('OPT_DIR', '../../lib/');
		define('DOC_DIR', '../../docs/input/');
		require_once(OPT_DIR.'opt.class.php');
		require_once(OPT_DIR.'opt.compiler.php');
		require_once(OPT_DIR.'opt.instructions.php');
		require_once(OPT_DIR.'opt.error.php');
	}

	class apiTest extends PHPUnit_Framework_TestCase
	{
	    protected $classes = array();
	    protected $methods = array();
	    protected $manuals = array();
	    protected $identifiers = array();
	    protected $loaded = false;
	    protected $missing = array();

	    protected function setUp()
	    {
			if(!$this->loaded)
			{
				$this->classes = array(
					'optClass' => new ReflectionClass('optClass'),
					'optCompiler' => new ReflectionClass('optCompiler'),
					'optCodeBuffer' => new ReflectionClass('optCodeBuffer'),
					'optAttribute' => new ReflectionClass('optAttribute'),
					'optNode' => new ReflectionClass('optNode'),
					'optScannable' => new ReflectionClass('optScannable'),
					'optCharacterData' => new ReflectionClass('optCharacterData'),
					'optText' => new ReflectionClass('optText'),
					'optElement' => new ReflectionClass('optElement'),
					'optExpression' => new ReflectionClass('optExpression'),
					'optRoot' => new ReflectionClass('optRoot'),	
					'optInstruction' => new ReflectionClass('optInstruction'),
					'optException' => new ReflectionClass('optException'),
				);

				if(file_exists(DOC_DIR.'en/manual.xml'))
				{
					$this->manuals['en'] = file_get_contents(DOC_DIR.'en/manual.xml');
				}
				if(file_exists(DOC_DIR.'pl/manual.xml'))
				{
					$this->manuals['pl'] = file_get_contents(DOC_DIR.'pl/manual.xml');
				}
				
				foreach($this->manuals as $lang => &$text)
				{
					if(preg_match_all('#id="([a-z0-9\.\-]*)"#', $text, $found))
					{
						$this->identifiers[$lang] = $found[1];
					}
				}
				$this->loaded = true;
			}
	    } // end setUp();
	 
	    protected function tearDown()
	    {
	        $this->tpl = NULL;
	    } // end tearDown();
	    
	    protected function inManual($id)
	    {
	    	$ok = true;
	    	foreach($this->identifiers as $lang => $list)
	    	{
	    		if(!in_array($id, $list))
	    		{
	    			$ok = false;
	    			$this->missing = $lang;
	    		}
	    	}
	    	return $ok;
	    } // end inManual();
	    
	    public static function methodProvider()
	    {
	    	return array(0 =>
	    		// optClass methods
	    		array(0 => 'optClass', 'assign', 'library.optclass.assign', M_PUBLIC),
	    		array(0 => 'optClass', 'assignGroup', 'library.optclass.assign-group', M_PUBLIC),
	    		array(0 => 'optClass', 'assignRef', 'library.optclass.assign-ref', M_PUBLIC),
	    		array(0 => 'optClass', 'assignDynamic', 'library.optclass.assign-dynamic', M_PUBLIC),
	    		array(0 => 'optClass', 'setup', 'library.optclass.setup', M_PUBLIC),
	    		array(0 => 'optClass', 'httpHeaders', 'library.optclass.http-headers', M_PUBLIC),
	    		array(0 => 'optClass', 'parse', 'library.optclass.parse', M_PUBLIC),
	    		array(0 => 'optClass', 'register', 'library.optclass.register', M_PUBLIC),
	    		array(0 => 'optClass', 'error', 'library.optclass.error', M_PUBLIC),
	    		array(0 => 'optClass', 'sendHeaders', 'library.optclass.send-headers', M_PUBLIC),
	    		array(0 => 'optClass', 'setHeader', 'library.optclass.set-header', M_PUBLIC),
	    		
	    		array(0 => 'optClass', '_convert', 'library.optclass._convert', M_PUBLIC),
	    		array(0 => 'optClass', '_compile', 'library.optclass._compile', M_PROTECTED),
	    		array(0 => 'optClass', '_recompile', 'library.optclass._recompile', M_PROTECTED),
	    		array(0 => 'optClass', '_parse', 'library.optclass._parse', M_PROTECTED),
	    		array(0 => 'optClass', '_preprocess', 'library.optclass._preprocess', M_PROTECTED),
	    		
	    		// optCompiler methods
	    		array(0 => 'optCompiler', '__construct', 'library.optcompiler.__construct', M_PUBLIC),
	    		array(0 => 'optCompiler', 'compile', 'library.optcompiler.compile', M_PUBLIC),
	    		array(0 => 'optCompiler', 'compileStage1', 'library.optcompiler.compile-stage-1', M_PUBLIC),
	    		array(0 => 'optCompiler', 'compileExpression', 'library.optcompiler.compile-expression', M_PUBLIC),
	    		array(0 => 'optCompiler', 'parseShortEntities', 'library.optcompiler.parse-short-entities', M_PUBLIC),
	    		array(0 => 'optCompiler', 'parseEntities', 'library.optcompiler.parse-entities', M_PUBLIC),
	    		array(0 => 'optCompiler', 'inNamespace', 'library.optcompiler.in-namespace', M_PUBLIC),
	    		array(0 => 'optCompiler', 'convert', 'library.optcompiler.convert', M_PUBLIC),
	    		array(0 => 'optCompiler', 'addConversionPattern', 'library.optcompiler.add-conversion-pattern', M_PUBLIC),  
	    		array(0 => 'optCompiler', 'removeConversionPattern', 'library.optcompiler.remove-conversion-pattern', M_PUBLIC),  
	    		array(0 => 'optCompiler', 'isIdentifier', 'library.optcompiler.is-identifier', M_PUBLIC),

	    		// optCodeBuffer methods
	    		array(0 => 'optCodeBuffer', 'addCode', 'library.optcodebuffer.add-code', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'shfCode', 'library.optcodebuffer.shf-code', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'copyBuffer', 'library.optcodebuffer.copy-buffer', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'getBuffer', 'library.optcodebuffer.get-buffer', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'bufferSize', 'library.optcodebuffer.buffer-size', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'buildCode', 'library.optcodebuffer.build-code', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'clean', 'library.optcodebuffer.clean', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'get', 'library.optcodebuffer.get', M_PUBLIC),
	    		array(0 => 'optCodeBuffer', 'set', 'library.optcodebuffer.set', M_PUBLIC),
	    		
	    		// optNode methods
	    		array(0 => 'optNode', 'getType', 'library.optnode.get-type', M_PUBLIC),
	    		array(0 => 'optNode', 'setParent', 'library.optnode.set-parent', M_PUBLIC),
	    		array(0 => 'optNode', 'getParent', 'library.optnode.get-parent', M_PUBLIC),
	    		
	    		// optAttribute methods
	    		array(0 => 'optAttribute', '__construct', 'library.optattribute.__construct', M_PUBLIC),
	    		array(0 => 'optAttribute', 'setName', 'library.optattribute.set-name', M_PUBLIC),
	    		array(0 => 'optAttribute', 'setNamespace', 'library.optattribute.set-namespace', M_PUBLIC),
	    		array(0 => 'optAttribute', 'setValue', 'library.optattribute.set-value', M_PUBLIC),
	    		array(0 => 'optAttribute', 'getName', 'library.optattribute.get-name', M_PUBLIC),
	    		array(0 => 'optAttribute', 'getXmlName', 'library.optattribute.get-xml-name', M_PUBLIC),
	    		array(0 => 'optAttribute', 'getValue', 'library.optattribute.get-value', M_PUBLIC),
	    		array(0 => 'optAttribute', 'getNamespace', 'library.optattribute.get-namespace', M_PUBLIC),
	    		
	    		// optScannable methods
	    		array(0 => 'optScannable', 'appendChild', 'library.optscannable.append-child', M_PUBLIC),
	    		array(0 => 'optScannable', 'insertBefore', 'library.optscannable.insert-before', M_PUBLIC),
	    		array(0 => 'optScannable', 'removeChild', 'library.optscannable.remove-child', M_PUBLIC),
	    		array(0 => 'optScannable', 'removeChildren', 'library.optscannable.remove-children', M_PUBLIC),
	    		array(0 => 'optScannable', 'replaceChild', 'library.optscannable.replace-child', M_PUBLIC),
	    		array(0 => 'optScannable', 'hasChildren', 'library.optscannable.has-children', M_PUBLIC),
	    		array(0 => 'optScannable', 'countChildren', 'library.optscannable.count-children', M_PUBLIC),
	    		array(0 => 'optScannable', 'getLastChild', 'library.optscannable.get-last-child', M_PUBLIC),
	    		array(0 => 'optScannable', 'getFlatElementsByTagName', 'library.optscannable.get-flat-elements-by-tag-name', M_PUBLIC),
	    		array(0 => 'optScannable', 'getFlatElementsByTagNameNS', 'library.optscannable.get-flat-elements-by-tag-name-ns', M_PUBLIC),
	    		array(0 => 'optScannable', 'getElementsByTagName', 'library.optscannable.get-elements-by-tag-name', M_PUBLIC),
	    		array(0 => 'optScannable', 'getElementsByTagNameNS', 'library.optscannable.get-elements-by-tag-name-ns', M_PUBLIC),
	    		array(0 => 'optScannable', 'clean', 'library.optscannable.clean', M_PUBLIC),
	    		
	    		// optCharacterData methods
	    		array(0 => 'optCharacterData', '__construct', 'library.optcharacterdata.__construct', M_PUBLIC),
	    		array(0 => 'optCharacterData', 'appendData', 'library.optcharacterdata.append-data', M_PUBLIC),
	    		array(0 => 'optCharacterData', 'insertData', 'library.optcharacterdata.insert-data', M_PUBLIC),
	    		array(0 => 'optCharacterData', 'replaceData', 'library.optcharacterdata.replace-data', M_PUBLIC),
	    		array(0 => 'optCharacterData', 'deleteData', 'library.optcharacterdata.delete-data', M_PUBLIC),
	    		array(0 => 'optCharacterData', 'substringData', 'library.optcharacterdata.substring-data', M_PUBLIC),
	    		
	    		// optText
	    		array(0 => 'optText', '__construct', 'library.opttext.__construct', M_PUBLIC),
	    		array(0 => 'optText', 'appendData', 'library.opttext.append-data', M_PUBLIC),
	    		
	    		// optElement methods
	    		array(0 => 'optElement', '__construct', 'library.optelement.__construct', M_PUBLIC),
	    		array(0 => 'optElement', 'setName', 'library.optelement.set-name', M_PUBLIC),
	    		array(0 => 'optElement', 'setNamespace', 'library.optelement.set-namespace', M_PUBLIC),
	    		array(0 => 'optElement', 'getName', 'library.optelement.get-name', M_PUBLIC),
	    		array(0 => 'optElement', 'getXmlName', 'library.optelement.get-xml-name', M_PUBLIC),
	    		array(0 => 'optElement', 'getNamespace', 'library.optelement.get-namespace', M_PUBLIC),
	    		array(0 => 'optElement', 'getAttributes', 'library.optelement.get-attributes', M_PUBLIC),
	    		array(0 => 'optElement', 'hasAttributes', 'library.optelement.has-attributes', M_PUBLIC),
	    		array(0 => 'optElement', 'addAttribute', 'library.optelement.add-attribute', M_PUBLIC),
	    		array(0 => 'optElement', 'removeAttribute', 'library.optelement.remove-attribute', M_PUBLIC),	    		
	    		
	    		// optExpression methods
	    		array(0 => 'optExpression', '__construct', 'library.optexpression.__construct', M_PUBLIC),
	    		
	    		// optRoot methods
	    		
	    		// optInstruction methods
	    		
	    		// optException methods
	    	);
	    
	    } // end provider();

 	   /**
 	    * @dataProvider methodProvider
 	    */
	    public function testMethod($class, $method, $manualId, $type)
	    {
			if(!$this->classes[$class]->hasMethod($method))
			{
				$this->fail('Method '.$method.' not found in '.$class);
			}
			$methodObj = $this->classes[$class]->getMethod($method);
			switch($type)
			{
				case M_PUBLIC:
					if(!$methodObj->isPublic())
					{
						$this->fail('Method '.$class.'::'.$method.' is not public.');
					}
					break;
				case M_PROTECTED:
					if(!$methodObj->isProtected())
					{
						$this->fail('Method '.$class.'::'.$method.' is not protected.');
					}
					break;
			}
			if(!$this->inManual($manualId))
			{
				$this->fail('Method '.$class.'::'.$method.' not found in "'.$this->missing.'" version of manual.');
			}
			return true;
	    } // end testMethod();
	} // end expressionTestSuite;
?>
