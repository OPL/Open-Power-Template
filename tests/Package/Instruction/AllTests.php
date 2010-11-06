<?php
/**
 * The test suite file that configures the execution of the test cases for
 * instructions.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

//require_once('AttributeTest.php');
//require_once('AttributesBuildTest.php');
require_once('BlockTest.php');
require_once('CaptureTest.php');
require_once('ComponentTest.php');
require_once('ContentTest.php');
require_once('DtdTest.php');
require_once('ExtendTest.php');
require_once('ForTest.php');
//require_once('ForeachTest.php');
require_once('GridTest.php');
require_once('IfTest.php');
require_once('IncludeTest.php');
//require_once('InsertTest.php');
require_once('LiteralTest.php');
require_once('LoadTest.php');
require_once('OmitTagTest.php');
require_once('ProcedureTest.php');
require_once('PrologTest.php');
//require_once('PutTest.php');
require_once('RepeatTest.php');
//require_once('RootTest.php');
require_once('SectionTest.php');
require_once('SelectorTest.php');
//require_once('ShowTest.php');
//require_once('SingleTest.php');
require_once('SnippetTest.php');
require_once('SwitchTest.php');
require_once('TagTest.php');
require_once('TreeTest.php');

class Package_Instruction_AllTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Configures the suite object.
	 *
	 * @return Suite
	 */
	public static function suite()
	{
		$suite = new Package_Instruction_AllTests('Package_Instruction');
		//$suite->addTestSuite('Package_Instruction_AttributeTest');
		//$suite->addTestSuite('Package_Instruction_AttributesBuildTest');
		$suite->addTestSuite('Package_Instruction_BlockTest');
		$suite->addTestSuite('Package_Instruction_CaptureTest');
		$suite->addTestSuite('Package_Instruction_ComponentTest');
		$suite->addTestSuite('Package_Instruction_ContentTest');
		$suite->addTestSuite('Package_Instruction_DtdTest');
		$suite->addTestSuite('Package_Instruction_ExtendTest');
		$suite->addTestSuite('Package_Instruction_ForTest');
		//$suite->addTestSuite('Package_Instruction_ForeachTest');
		$suite->addTestSuite('Package_Instruction_GridTest');
		$suite->addTestSuite('Package_Instruction_IfTest');
		$suite->addTestSuite('Package_Instruction_IncludeTest');
		//$suite->addTestSuite('Package_Instruction_InsertTest');
		$suite->addTestSuite('Package_Instruction_LiteralTest');
		$suite->addTestSuite('Package_Instruction_LoadTest');
		$suite->addTestSuite('Package_Instruction_OmitTagTest');
		$suite->addTestSuite('Package_Instruction_ProcedureTest');
		$suite->addTestSuite('Package_Instruction_PrologTest');
		//$suite->addTestSuite('Package_Instruction_PutTest');
		$suite->addTestSuite('Package_Instruction_RepeatTest');
		//$suite->addTestSuite('Package_Instruction_RootTest');
		$suite->addTestSuite('Package_Instruction_SectionTest');
		$suite->addTestSuite('Package_Instruction_SelectorTest');
		//$suite->addTestSuite('Package_Instruction_ShowTest');
		//$suite->addTestSuite('Package_Instruction_SingleTest');
		$suite->addTestSuite('Package_Instruction_SnippetTest');
		$suite->addTestSuite('Package_Instruction_SwitchTest');
		$suite->addTestSuite('Package_Instruction_TagTest');
		$suite->addTestSuite('Package_Instruction_TreeTest');

		return $suite;
	} // end suite();

	/**
	 * Configures the OPL autoloader and installs the libraries.
	 */
	protected function setUp()
	{
		/* currently null */
	} // end setUp();

	/**
	 * Shuts down the test procedure.
	 */
	protected function tearDown()
	{
		/* currently null */
	} // end tearDown();

} // end Package_Instruction_AllTests;