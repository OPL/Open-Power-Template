<?php

	class optCompilerTester extends optInstruction
	{
		private $level = 0;
		public function configure()
		{
			$this -> sectionDirection = array();
			return array(
				// processor name
				0 => 'compiler',
				// instructions
				'compiler' => OPT_MASTER,
				'/compiler' => OPT_ENDER
			);
		} // end configure();
		
		public function instructionNodeProcess(ioptNode $node)
		{
			foreach($node as $block)
			{
				switch($block -> getName())
				{
					case 'compiler':
							$this -> compiler -> out("BEGIN COMPILER SESSION\r\n", true);
							foreach($block as $subNode)
							{
								$this -> hardcoreTreeProcess($subNode);
							}
							break;
					case '/compiler':
							$this -> compiler -> out("END COMPILER SESSION", true);
							break;
				}
			}
		} // end process();

		public function hardcoreTreeProcess(ioptNode $node)
		{
			foreach($node as $block)
			{
				$attributes = $block->getAttributes();
				if(!isset($attributes[3]))
				{
					$attributes[3] = '';
				}
				switch($block -> getType())
				{
					case OPT_MASTER:
						$this -> compiler -> out(str_repeat('.',$this->level).'MASTER: '.$block->getName()." (".$attributes[3].")(".$node->getName().")\r\n", true);
						$this -> level++;
						break;
					case OPT_ENDER:
						$this -> level--;
						$this -> compiler -> out(str_repeat('.',$this->level).'ENDER: '.$block->getName()." (".$attributes[3].")(".$node->getName().")\r\n", true);
						break;
					case OPT_COMMAND:
						$this -> compiler -> out(str_repeat('.',$this->level).'CMD: '.$block->getName()." (".$attributes[3].")(".$node->getName().")\r\n", true);
						break;
					case OPT_ALT:
						$this -> compiler -> out(str_repeat('.',$this->level-1).'ALT: '.$block->getName()." (".$attributes[3].")(".$node->getName().")\r\n", true);
						break;				
				}
				foreach($block as $subNode)
				{
					if($node -> getType() != OPT_TEXT)
					{
						$this -> hardcoreTreeProcess($subNode);
					}
				}
			}
		} // end defaultTreeProcess();	
	}
	
	class optFakeSection
	{
		public $nesting = 1;
		public $sectionList = array(0 => 'section');
	}
	
	class optEscaper extends optInstruction
	{
		public function configure()
		{
			return array(
				// processor name
				0 => 'escaper',
				// instructions
				'escaper' => OPT_MASTER,
				'/escaper' => OPT_ENDER
			);
		} // end configure();
		
		public function instructionNodeProcess(ioptNode $node)
		{
			foreach($node as $block)
			{
				switch($block -> getName())
				{
					case 'escaper':
							$this -> start($block);
							$this -> defaultTreeProcess($block);
							break;
					case '/escaper':
							$this -> stop();
							break;
				}
			}
		} // end process();
		
		public function start($block)
		{
			$params = array(
				'par1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION, NULL)			
			);
		
			$this -> compiler -> parametrize('escaper', $block->getAttributes(), $params);
			$this -> compiler -> out(' Escaper started: '.$params['par1'].'<br/>', true);	
		} // end start();
		
		public function stop()
		{	
			$this -> compiler -> out(' Escaper stopped<br/>', true);
		} // end stop();
	}

	class optTestParser extends optApi
	{
		public function __construct()
		{
			$this -> control = array(0 => 'optCompilerTester', 'optEscaper');
			$this -> functions['checkrole'] = 'checkrole';
			$this -> functions['menuperms'] = 'menuperms';
			$this -> xmlsyntaxMode = true;
			$this -> compiler = new optCompiler($this);
		} // end __construct();
	
		public function codeParse($code)
		{
			return $this -> compiler -> parse($code);
		} // end doParse();
	
		protected function doInclude($filename, $default = false)
		{
			// actually do nothing at the moment
		} // end doInclude();
	}

	class optCompilerTest extends PHPUnit_TestCase
	{
		private $opt;
		
		public function __construct($name)
		{
			$this -> PHPUnit_TestCase($name);
		} // end __construct();
		
		public function setUp()
		{
			$this -> opt = new optTestParser;
		} // end setUp();
		
		public function tearDown()
		{
			unset($this -> opt);		
		} // end tearDown();

		public function testExpressionStrings()
		{
			try
			{
				$this -> assertEquals('"A string"', $this->opt->compiler->compileExpression('"A string"'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionStrings();
		
		public function testExpressionEscapedStrings()
		{
			try
			{
				$this -> assertEquals('"A \"string"', $this->opt->compiler->compileExpression('"A \"string"'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}			
		} // end testExpressionEscapedStrings();
		
		public function testExpressionRAStrings()
		{
			try
			{
				$this -> assertEquals('\'A string\'', $this->opt->compiler->compileExpression('`A string`'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}			
		} // end testExpressionRAStrings();
		
		public function testExpressionRAEscapedStrings()
		{
			try
			{
				$this -> assertEquals('\'A "string\'', $this->opt->compiler->compileExpression('`A "string`'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionRAEscapedStrings();
		
		public function testExpressionRAEscapedStringsRA()
		{
			try
			{
				$this -> assertEquals('\'A `string\'', $this->opt->compiler->compileExpression('`A \`string`'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}			
		} // end testExpressionRAEscapedStrings();
		
		public function testExpressionNonOperatorStrings()
		{
			try
			{
				$this -> assertEquals('\'edit\'', $this->opt->compiler->compileExpression('edit'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionNonOperatorStrings();
		
		public function testExpressionOperatorStrings()
		{
			try
			{
				$this -> assertEquals('5+3', $this->opt->compiler->compileExpression('5 add 3'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionOperatorStrings();
		
		public function testExpressionIncOperatorGood()
		{
			try
			{
				$this -> assertEquals('$this->data[\'a\']++', $this->opt->compiler->compileExpression('$a++'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionIncOperatorGood();
		
		public function testExpressionIncOperatorBad()
		{
			try
			{
				// Currently not supported
				$this->opt->compiler->compileExpression('++$a');
			}
			catch(optException $exc)
			{
				return 1;				
			}
			$this -> fail('Exception not returned');
		} // end testExpressionIncOperatorGood();
		
		public function testExpressionNumbers()
		{
			try
			{
				$this -> assertEquals('12345', $this->opt->compiler->compileExpression('12345'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionNumbers();
		
		public function testExpressionFloatNumbers()
		{
			try
			{
				$this -> assertEquals('12345.67', $this->opt->compiler->compileExpression('12345.67'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionFloatNumbers();
		
		public function testExpressionHexadecimalNumbers()
		{
			try
			{
				$this -> assertEquals('0x54A6B', $this->opt->compiler->compileExpression('0x54A6B'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionHexadecimalNumbers();
		
		public function testExpressionLostBracketTest()
		{
			try
			{
				$this->opt->compiler->compileExpression('($a + ($b - $c) * $d');
			}
			catch(optException $exception)
			{
				return 1;
			}
			$this -> fail('Lost bracket exception not returned!');
		} // end testExpressionLostBracketTest();
		
		public function testExpressionNullFunction()
		{
			try
			{
				$this -> assertEquals('optcheckrole($this)', $this->opt->compiler->compileExpression('checkrole()'));
			}
			catch(optException $exception)
			{
				optErrorHandler($exception);
				$this -> fail('Exception returned');
			}			
		} // end testExpressionNullFunction();

		public function testExpressionFunctionWithParams()
		{
			try
			{
				$this -> assertEquals('optcheckrole($this,$this->data[\'a\'],$this->data[\'b\'])', $this->opt->compiler->compileExpression('checkrole($a, $b)'));
			}
			catch(optException $exception)
			{
				optErrorHandler($exception);
				$this -> fail('Exception returned');
			}
		} // end testExpressionFunctionWithParams();

		public function testExpressionNullMethod()
		{
			try
			{
				$this -> assertEquals('$this->data[\'a\']->checkrole()', $this->opt->compiler->compileExpression('$a->checkrole()'));
			}
			catch(optException $exception)
			{
				optErrorHandler($exception);
				$this -> fail('Exception returned');
			}			
		} // end testExpressionNullMethod();

		public function testExpressionMethodWithParams()
		{
			try
			{
				$this -> assertEquals('$this->data[\'a\']->checkrole($this->data[\'a\'],$this->data[\'b\'])', $this->opt->compiler->compileExpression('$a->checkrole($a, $b)'));
			}
			catch(optException $exception)
			{
				optErrorHandler($exception);
				$this -> fail('Exception returned');
			}
		} // end testExpressionMethodWithParams();
		
		public function testExpressionTablePHPSyntax()
		{
			try
			{
				$this -> assertEquals('$this->data[\'block\'][5][$this->data[\'b\']]', $this->opt->compiler->compileExpression('$block[5][$b]'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionTablePHPSyntax();
		
		public function testExpressionTableAlternativeSyntax()
		{
			try
			{
				$this -> assertEquals('$this->data[\'block\'][5][$this->data[\'b\']]', $this->opt->compiler->compileExpression('$block.5[$b]'));		
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionTablePHPSyntax();
		
		public function testExpressionSectionSyntax()
		{
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> assertEquals('$__section_val[\'block\']', $this->opt->compiler->compileExpression('$section.block'));		
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionSectionSyntax();
		
		public function testExpressionSectionSyntax2()
		{
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> assertEquals('$this->data[\'table\'][\'block\']', $this->opt->compiler->compileExpression('$table.block'));		
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}	
		} // end testExpressionSectionSyntax();
		
		public function testExpressionAssignmentBasic()
		{
			try
			{
				$result = $this->opt->compiler->compileExpression('$a = 17', 1);
				$this -> assertEquals('$this->data[\'a\']=17', $result[0]);
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionAssignmentBasic();
		
		public function testExpressionMultiAssignment()
		{
			try
			{
				$result = $this->opt->compiler->compileExpression('$a = $b = $c = 17', 1);
				$this -> assertEquals('$this->data[\'a\']=$this->data[\'b\']=$this->data[\'c\']=17', $result[0]);
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionMultiAssignment();
		
		public function testExpressionExtendedAssignment()
		{
			try
			{
				$result = $this->opt->compiler->compileExpression('$a[$b + $c] = 17', 1);
				$this -> assertEquals('$this->data[\'a\'][$this->data[\'b\']+$this->data[\'c\']]=17', $result[0]);
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testExpressionExtendedAssignment();
		
		public function testExpressionInvalidAssignment()
		{
			try
			{
				$this->opt->compiler->compileExpression('$b + $c = 17', 1);
			}
			catch(optException $exc)
			{
				return 1;
			}
			$this -> fail('Invalid assignment exception not returned!');
		} // end testExpressionInvalidAssignment();

		public function testRealExpression1()
		{
			// Expression sent by eXtreme (http://exsite.edigo.pl)
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'Posts');
				$this -> assertEquals('!$__Posts_val[\'is_topic_start\']&&((optcheckrole($this,"board_delete_own_posts")&&$this->'.
'vars[\'timeFromPosting\']<=2&&$__Posts_val[\'user_id\']==$this->'.
'data[\'UserData\'][\'id\']&&!$__Posts_val[\'is_moderated\'])||(optcheckrole($this,'.
'"board_delete_all_time_own_posts")&&$__Posts_val[\'user_id\']==$this->'.
'data[\'UserData\'][\'id\']&&!$__Posts_val[\'is_moderated\'])||optcheckrole($this,"board_can_moderate"))',
					$this->opt->compiler->compileExpression('not $Posts.is_topic_start && ((checkrole("board_delete_own_posts") && @timeFromPosting <= 2 && $Posts.user_id == $UserData[id] && not $Posts.is_moderated) || (checkrole("board_delete_all_time_own_posts") && $Posts.user_id == $UserData[id] && not $Posts.is_moderated) || checkrole("board_can_moderate"))'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression1();

		public function testRealExpression2()
		{
			// Expression sent by eXtreme (http://exsite.edigo.pl)
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'Topics');
				$this -> assertEquals('$this->data[\'ReadTopics\'][$__Topics_val[\'id\']]&&$this->'.
'data[\'ReadTopics\'][$__Topics_val[\'id\']][\'content\']==$this->data[\'Forum\'][\'id\'].":1"',
					$this->opt->compiler->compileExpression('$ReadTopics[$Topics.id] && $ReadTopics[$Topics.id][content] == $Forum[id]::":1"'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression2();

		public function testRealExpression3()
		{
			// Expression sent by eXtreme (http://exsite.edigo.pl)
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'Posts');
				$this -> assertEquals('(optcheckrole($this,"board_edit_own_posts")&&$this->'.
'vars[\'timeFromPosting\']<=5&&$__Posts_val[\'user_id\']==$this->'.
'data[\'UserData\'][\'id\']&&!$__Posts_val[\'is_moderated\'])||(optcheckrole($this,'.
'"board_edit_all_time_own_posts")&&$__Posts_val[\'user_id\']==$this->'.
'data[\'UserData\'][\'id\']&&!$__Posts_val[\'is_moderated\'])||optcheckrole($this,"board_can_moderate")',
					$this->opt->compiler->compileExpression('(checkrole("board_edit_own_posts") && @timeFromPosting <= 5 && $Posts.user_id == $UserData[id] && not $Posts.is_moderated) || (checkrole("board_edit_all_time_own_posts") && $Posts.user_id == $UserData[id] && not $Posts.is_moderated) || checkrole("board_can_moderate")'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression3();
		
		public function testRealExpression4()
		{
			// Expression sent by eXtreme (http://exsite.edigo.pl)
			try
			{
				$this -> assertEquals('($this->vars[\'Mval\']->positions->item&&($this->'.
'vars[\'Mval\']->positions[\'show\']==\'yes\'||($this->vars[\'Mval\']->'.
'positions[\'show\']==\'selected\'&&$this->data[\'ExpandMenuId\']==$this->'.
'vars[\'Mval\'][\'id\']))&&(!$this->vars[\'Mval\']->positions[\'logged_in\']||($this->'.
'vars[\'Mval\']->positions[\'logged_in\']=="no"&&$this->data[\'UserNotLoggedIn\'])||($this->'.
'vars[\'Mval\']->positions[\'logged_in\']=="yes"&&$this->data[\'UserLoggedIn\'])||$this->'.
'vars[\'Mval\']->positions[\'logged_in\']=="all"))&&((!$this->vars[\'Mval\']->positions->'.
'checkperms)||($this->vars[\'Mval\']->positions->checkperms&&optmenuperms($this,$this->'.
'vars[\'Mval\']->positions->checkperms)))',
					$this->opt->compiler->compileExpression('(@Mval->positions->item && (@Mval->positions[show] == \'yes\' || (@Mval->positions[show] == \'selected\' && $ExpandMenuId == @Mval[id])) && (not @Mval->positions[logged_in] || (@Mval->positions[logged_in] == "no" && $UserNotLoggedIn) || (@Mval->positions[logged_in] == "yes" && $UserLoggedIn) || @Mval->positions[logged_in] == "all")) && ((not @Mval->positions->checkperms) || (@Mval->positions->checkperms && menuperms(@Mval->positions->checkperms)))'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}
		} // end testRealExpression4();
		
		public function testRealExpression5()
		{
			// Expression sent by Denver
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'ranks');
				$this -> assertEquals('$this->vars[\'prank\']==$__ranks_val[\'id\']',
					$this->opt->compiler->compileExpression('@prank==$ranks.id'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression5();
		
		public function testRealExpression6()
		{
			// Expression sent by Denver
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'ranks');
				$this -> assertEquals('$__ranks_val[\'id\']==$this->vars[\'prank\']',
					$this->opt->compiler->compileExpression('$ranks.id==@prank'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression6();


		public function testRealExpression7()
		{
			// Expression sent by eXtreme (http://exsite.edigo.pl)
			try
			{
				$this -> opt -> compiler -> processors['section'] = new optFakeSection;
				$this -> opt -> compiler -> processors['section'] -> sectionList = array(0 => 'Posts');
				$this -> assertEquals('($this->vars[\'gmttime\']-$__Posts_val[\'date\'])/60',
					$this->opt->compiler->compileExpression('(@gmttime-$Posts.date)/60'));
			}
			catch(optException $exc)
			{
				optErrorHandler($exc);
				$this -> fail('Exception returned');
			}		
		} // end testRealExpression7();

		public function testParametrizeNoParamsNoMatches()
		{
			try
			{
				$params = array();
				
				$matches = array();
	
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($parsingResult == array() && count($params) == 0);
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}	
		} // end testParametrizeNoParamsNoMatches();
		
		public function testParametrizeNoParamsYesMatchesUnnamed()
		{
			try
			{
				$params = array();
				
				$matches = array(
					3 => '=blablabla; trelele;',
					4 => 'blablabla; trelele;'			
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($parsingResult == array() && count($params) == 0);
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}	
		} // end testParametrizeNoParamsYesMatchesUnnamed();
		
		public function testParametrizeNoParamsYesMatchesNamed()
		{
			try
			{
				$params = array();
				
				$matches = array(
					3 => ' param1="blablabla" param2="trelele"',
					4 => 'param1="blablabla" param2="trelele"'			
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($parsingResult == array() && count($params) == 0);
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}	
		} // end testParametrizeNoParamsYesMatchesNamed();
		
		public function testParametrizeYesOptionalParamsNoMatches()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'abc'),
					'param2' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'bcd')
				);
				
				$matches = array();
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($parsingResult == array() && $params == array('param1' => 'abc', 'param2' => 'bcd'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}	
		} // end testParametrizeYesOptionalParamsNoMatches();
		
		public function testParametrizeYesRequiredParamsNoMatches()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID)
				);
				
				$matches = array();
				$this->opt->compiler->parametrize('testCase', $matches, $params);
			}
			catch(optException $exception)
			{
				if($exception -> getCode() == OPT_E_REQUIRED_NOT_FOUND)
				{
					return 1;
				}
			}
			$this -> fail('Exception not returned');
		} // end testParametrizeYesOptionalParamsNoMatches();
		
		public function testParametrizeYesRequiredParamsYesMatchesUnnamed()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID)
				);
				
				$matches = array(
					3 => '=abc; bcd',
					4 => 'abc; bcd'
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(count($parsingResult) == 0 && $params == array('param1' => 'abc', 'param2' => 'bcd'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}		
		} // end testParametrizeYesRequiredParamsYesMatchesUnnamed();
		
		public function testParametrizeYesRequiredParamsYesMatchesNamed()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID)
				);
				
				$matches = array(
					3 => ' param1="abc" param2="bcd"',
					4 => 'param1="abc" param2="bcd"'	
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($parsingResult == array() && $params == array('param1' => 'abc', 'param2' => 'bcd'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}		
		} // end testParametrizeYesRequiredParamsYesMatchesNamed();
		
		public function testParametrizeYesRequiredAndOptionalParamsYesMatchesUnnamed()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'bcd')
				);
				
				$matches = array(
					3 => '=abc; def',
					4 => 'abc; def'			
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(count($parsingResult) == 0 && $params == array('param1' => 'abc', 'param2' => 'def'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}		
		} // end testParametrizeYesRequiredAndRequiredParamsYesMatchesUnnamed();

		public function testParametrizeYesRequiredAndOptionalParamsYesIncompleteMatchesUnnamed()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'bcd')
				);
				
				$matches = array(
					3 => '=abc',
					4 => 'abc'			
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(count($parsingResult) == 0 && $params == array('param1' => 'abc', 'param2' => 'bcd'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}		
		} // end testParametrizeYesRequiredAndRequiredParamsYesIncompleteMatchesUnnamed();
		
		public function testParametrizeOptionalJump()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'bcd'),
					'param3' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'cde')
				);
				
				$matches = array(
					3 => '=abc; !x; def',
					4 => 'abc; !x; def'	
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(count($parsingResult) == 0 && $params == array('param1' => 'abc', 'param2' => 'bcd', 'param3' => 'def'));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}		
		} // end testParametrizeOptionalJump();
		
		public function testParametrizeOptionalJumpAtRequired()
		{
			try{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'param3' => array(OPT_PARAM_OPTIONAL, OPT_PARAM_ID, 'cde')
				);

				$matches = array(
					3 => '=abc; !x; def',
					4 => 'abc; !x; def'
				);
				$parsingResult = $this->opt->compiler->parametrize('testCase', $matches, $params);
			}
			catch(optException $exception)
			{
				if($exception -> getCode() == OPT_E_DEFAULT_MARKER)
				{
					return 1;
				}
			}
			$this -> fail('Invalid marker exception not returned!');
		} // end testParametrizeOptionalJump();

		public function testParametrizeEscapingUnnamed1()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION)
				);

				$matches = array(
					3 => '=$abc + $cba; $bcb + 8',
					4 => '$abc + $cba; $bcb + 8'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(
					$params['param1'] == '$this->data[\'abc\']+$this->data[\'cba\']' &&
					$params['param2'] == '$this->data[\'bcb\']+8'
				);
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}
		} // end testParametrizeEscapingUnnamed1();
		
		public function testParametrizeEscapingUnnamed2()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION)
				);
				
				$matches = array(
					3 => '=$abc::`Text with ; semicolon`; $bcb + 8',
					4 => '$abc::`Text with ; semicolon`; $bcb + 8'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(
					$params['param1'] == '$this->data[\'abc\'].\'Text with ; semicolon\'' &&
					$params['param2'] == '$this->data[\'bcb\']+8'
				);				
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}
		} // end testParametrizeEscapingUnnamed2();

		public function testParametrizeEscapingUnnamed3()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION),
					'param2' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION)
				);
				
				$matches = array(
					3 => '=$abc::\'Text with ; semicolon\'; $bcb + 8',
					4 => '$abc::\'Text with ; semicolon\'; $bcb + 8'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue(
					$params['param1'] == '$this->data[\'abc\'].\'Text with ; semicolon\'' &&
					$params['param2'] == '$this->data[\'bcb\']+8'
				);				
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}
		} // end testParametrizeEscapingUnnamed3();
		
		public function testParametrizeEscapingNamed()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_EXPRESSION)
				);
				
				$matches = array(
					3 => ' param1="$abc::\"Sample text\""',
					4 => 'param1="$abc::\"Sample text\""'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertEquals($params['param1'], '$this->data[\'abc\']."Sample text"');				
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned: '.$exception->getCode().' ('.$exception->getMessage().')');
			}
		} // end testParametrizeEscapingNamed();
		
		public function testParametrizeUndefinedParameters()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
					'__UNKNOWN__' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
				);
				
				$matches = array(
					3 => ' param1="value 1" param2="value 2" param3="value 3"',
					4 => 'param1="value 1" param2="value 2" param3="value 3"'
				);
				$undef = $this->opt->compiler->parametrize('testCase', $matches, $params);
				$this -> assertTrue($params['param1'] == 'value 1' && $undef['param2'] == 'value 2' && $undef['param3'] == 'value 3');				
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned: '.$exception->getCode().' ('.$exception->getMessage().')');
			}
		} // end testParametrizeEscapingNamed();
		
		public function testParametrizeInvalidStyleTest1()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
				);
				
				$matches = array(
					3 => ' param1="value 1"',
					4 => 'param1="value 1"'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params, OPT_STYLE_OPT);
			}
			catch(optException $exception)
			{
				return 1;
			}
			$this -> fail('Exception not returned');
		} // end testParametrizeInvalidStyleTest1();
		
		public function testParametrizeInvalidStyleTest2()
		{
			try
			{
				$params = array(
					'param1' => array(OPT_PARAM_REQUIRED, OPT_PARAM_ID),
				);
				
				$matches = array(
					3 => '=value 1',
					4 => 'value 1'
				);
				$this->opt->compiler->parametrize('testCase', $matches, $params, OPT_STYLE_XML);
			}
			catch(optException $exception)
			{
				return 1;
			}
			$this -> fail('Exception not returned');
		} // end testParametrizeInvalidStyleTest2();

		public function testCompilerSimple()
		{
$template = '{compiler}
{ava}
ppp
{avaelse}
ppp
{/ava}
{/compiler}';
$result = 'BEGIN COMPILER SESSION
MASTER: ava ()(ava)
ALT: avaelse ()(ava)
ENDER: /ava ()(ava)
END COMPILER SESSION';
			$this -> assertEquals($result, $this->opt->compiler->parse(NULL, $template));
		} // end testCompilerSimple();
		
		public function testCompilerCommands()
		{
$template = '{compiler}
{permate/}
{/compiler}';
$result = 'BEGIN COMPILER SESSION
CMD: permate ()(permate)
END COMPILER SESSION';
			$this -> assertEquals($result, $this->opt->compiler->parse(NULL, $template));		
		} // end testCompilerCommands();
		
		public function testCompilerMegadeath()
		{
			try
			{
$template = '{compiler}
{sect1=test}
	{sect2=hope}
		{thereishope=miracle/}
	{sect2else}
		{thereisnohope/}
	{/sect2}
{/sect1}
{/compiler}';
$result = 'BEGIN COMPILER SESSION
MASTER: sect1 (=test)(sect1)
.MASTER: sect2 (=hope)(sect2)
..CMD: thereishope (=miracle)(thereishope)
.ALT: sect2else ()(sect2)
..CMD: thereisnohope ()(thereisnohope)
.ENDER: /sect2 ()(sect2)
ENDER: /sect1 ()(sect1)
END COMPILER SESSION';
			$this -> assertEquals($result, $this->opt->compiler->parse(NULL, $template));
			}
			catch(optException $exception)
			{
				$this -> fail('Exception returned');
			}					
		} // end testCompilerMegadeath();
		
		public function testCompilerInvalidTree()
		{
$template = '{sect1=test}
	{sect2=hope}
		{thereishope=miracle/}
	{/sect2}
	{/sect1}
	{/alaa}
';

			try
			{
				$parsingResult = $this->opt->compiler->parse(NULL, $template);
			}
			catch(optException $exception)
			{
				if($exception -> getCode() == OPT_E_ENCLOSING_STATEMENT)
				{
					return 1;
				}
			}
			$this -> fail('Exception not returned!');	
		} // end testCompilerInvalidTree();
		
		public function testCompilerInvalidEnclosingTag()
		{
$template = '{compiler}{tag1}
{tag2}
{tag3}
{/tag2}
{/tag3}
{/tag1}{/compiler}';
			$parsingResult = '';
			try
			{
				$parsingResult = $this->opt->compiler->parse(NULL, $template);				
			}
			catch(optException $exception)
			{
				if($exception -> getCode() == OPT_E_ENCLOSING_STATEMENT)
				{
					return 1;
				}
			}
			$this -> fail("Exception not returned! The result: \r\n".$parsingResult);	
		} // end testCompilerInvalidEnclosingTag();
		
		public function testStaticTextEntities()
		{
			try
			{
				$this -> assertEquals('foo { } bar', $this->opt->compiler->parse(NULL, 'foo &lb; &rb; bar'));
			}
			catch(optException $exc)
			{
				$this -> fail('Exception returned: '.$exc -> getMessage());
			}
		} // end testStaticTextEntities();
		
		public function testDynamicTextEntities()
		{
			$this -> opt -> entities = true;
			try
			{
				$this -> assertEquals('<'.'?php echo "This is a text with entities: { } < >"."another text".\'another text\'; ?'.'>', $this->opt->compiler->parse(NULL, '{"This is a text with entities: &lb; &rb; &lt; &gt;"::&quot;another text&quot;::&apos;another text&apos;}'));
			}
			catch(optException $exc)
			{
				$this -> fail('Exception returned: '.$exc -> getMessage());
			}
		} // end testStaticTextEntities();

		public function testTagWhitespaces()
		{
			try
			{
				$result = $this->opt->compiler->parse(NULL, '{compiler	
					test="foo"
						bar = "bar" } {/compiler}');
				$template = "BEGIN COMPILER SESSION\r\nEND COMPILER SESSION";
  				$this -> assertEquals($result, $template);
			}
			catch(optException $exc)
			{
				$this -> fail('Exception returned: '.$exc -> getMessage());
			}
		} // end testStaticTextEntities();
		
		public function testUnusedNamespaces()
		{
			try
			{
				$template = '<test foo:attribute="{$variable}" opt:attribute="{$value}"/>';
				$result = $this->opt->compiler->parse(NULL, $template);
				$this -> assertEquals('<test foo:attribute="<'.'?php echo $this->data[\'variable\']; ?'.'>" opt:attribute="{$value}"/>', $result);
			}
			catch(optException $exc)
			{
				$this -> fail('Exception returned: '.$exc->getMessage());
			}			
		} // end testUnusedNamespaces();
	}

?>
