<?php
/**
 * The tests for opt:component instruction.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once('./Extra/TestFS.php');
require_once('./Extra/TestFSBase.php');

/**
 * @covers Opt_Compiler_Class
 * @covers Opt_Compiler_Format
 * @covers Opt_Compiler_Processor
 * @covers Opt_Instruction_Component
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_ComponentTest extends Extra_TestFSBase
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
	 * @return Array
	 */
	public static function dataProvider()
	{
		return array(0 =>
			array('Component/component_basic.txt'),
			array('Component/component_datasource.txt'),
			array('Component/component_display.txt'),
			array('Component/component_display_attr.txt'),
			array('Component/component_events.txt'),
			array('Component/component_management.txt'),
			array('Component/component_multiple.txt'),
			array('Component/component_defined.txt'),
			array('Component/component_param_empty.txt'),
			array('Component/component_skipping_opt.txt'),
			array('Component/component_inject_procedure.txt'),
			array('Component/component_inject_snippet.txt'),
			array('Component/component_nesting.txt'),
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
} // end Package_Instruction_ComponentTest;