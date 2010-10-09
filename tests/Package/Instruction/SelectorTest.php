<?php
/**
 * The tests for opt:selector instruction.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2010 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once('./Extra/TestFS.php');
require_once('./Extra/TestFSBase.php');

/**
 * @covers Opt_Compiler_Class
 * @covers Opt_Compiler_Format
 * @covers Opt_Compiler_Processor
 * @covers Opt_Instruction_Section_Abstract
 * @covers Opt_Instruction_Selector
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_SelectorTest extends Extra_TestFSBase
{

	/**
	 * Configuration method.
	 * @param Opt_Class $tpl
	 */
	public function configure(Opt_Class $tpl)
	{
		$tpl->parser = 'Opt_Parser_Xml';
		$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:myComponent', 'Extra_Mock_Component');
		$tpl->register(Opt_Class::OPT_BLOCK, 'opt:myBlock', 'Extra_Mock_Block');
	} // end configure();

	/**
	 * Provides the list of test cases.
	 * @return array
	 */
	public static function dataProvider()
	{
		return array(0 =>
			array('Selector/selector_basic.txt'),
			array('Selector/selector_test.txt'),
			array('Selector/selector_show.txt'),
			array('Selector/selector_show_test.txt'),
			array('Selector/selector_else.txt'),
			array('Selector/selector_multiple_else.txt'),
			array('Selector/selector_separator.txt'),
			array('Selector/selector_attribute.txt'),
			array('Selector/selector_attribute_separator.txt'),
		);
	} // end dataProvider();

 	/**
 	 * @dataProvider dataProvider
	 * @runInSeparateProcess
 	 */
	public function testInstructions($testCase)
	{
		return $this->_checkTest(dirname(__FILE__).'/Tests/'.$testCase);
	} // end testInstructions();
} // end Package_Instruction_SelectorTest;