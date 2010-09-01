<?php
/**
 * The tests for opt:dtd instruction.
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
 * @covers Opt_Instruction_Dtd
 * @runTestsInSeparateProcesses
 */
class Package_Instruction_DtdTest extends Extra_TestFSBase
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
			array('Dtd/dtd_custom.txt'),
			array('Dtd/dtd_multiple.txt'),
			array('Dtd/dtd_unknown.txt'),
			array('Dtd/dtd_xhtml10transitional.txt'),
			array('Dtd/dtd_xhtml10strict.txt'),
			array('Dtd/dtd_xhtml10frameset.txt'),
			array('Dtd/dtd_xhtml11.txt'),
			array('Dtd/dtd_html40.txt'),
			array('Dtd/dtd_html5.txt'),
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
} // end Package_Instruction_DtdTest;