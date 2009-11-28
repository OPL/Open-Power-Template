<?php
/**
 * The tests for Opt_View.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers Opt_View
 */
class Package_ViewTest extends Extra_Testcase
{
	/**
	 * The OPT class object
	 * @var Opt_Class
	 */
	private $_tpl;

	/**
	 * Prepare a new Opt_Class object for tests.
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->_tpl = new Opt_Class;
		$this->_tpl->sourceDir = './templates/';
		$this->_tpl->compileDir = './templates_c/';
		$this->_tpl->setup();
		// @codeCoverageIgnoreStop
	} // end setUp();

	/**
	 * Free the existing Opt_Class object.
	 */
	protected function tearDown()
	{
		Opl_Registry::register('opt', null);
		unset($this->_tpl);
	} // end tearDown();

	/**
	 * @covers Opt_View::__construct
	 * @covers Opt_View::getTemplate
	 */
	public function testConstructor()
	{
		$view = new Opt_View('template.tpl');

		$this->assertEquals('template.tpl', $view->getTemplate());
	} // end testConstructor();

	/**
	 * @covers Opt_View::__construct
	 * @covers Opt_View::getCache
	 */
	public function testConstructorGetsCacheFromOpt()
	{
		$this->_tpl->setCache($obj = $this->getMock('Opt_Caching_Interface'));
		$view = new Opt_View('template.tpl');

		$this->assertSame($obj, $view->getCache());
	} // end testConstructorGetsCacheFromOpt();

	/**
	 * @covers Opt_View::setBranch
	 * @covers Opt_View::getBranch
	 */
	public function testSettingBranches()
	{
		$view = new Opt_View('template.tpl');
		$view->setBranch('branch');

		$this->assertEquals('branch', $view->getBranch());
	} // end testSettingBranches();

	/**
	 * @covers Opt_View::__set
	 * @covers Opt_View::__get
	 */
	public function testAssignMagicMethods()
	{
		$view = new Opt_View('template.tpl');
		$view->variable = 'Foo';
		$this->assertEquals('Foo', $view->variable);
	} // end testAssignMagicMethods();

	/**
	 * @covers Opt_View::__set
	 * @covers Opt_View::__get
	 * @covers Opt_View::__unset
	 */
	public function testUnsetVariableReturnsNull()
	{
		$view = new Opt_View('template.tpl');
		$view->variable = 'Foo';
		unset($view->variable);
		$this->assertEquals(null, $view->variable);
	} // end testUnsetVariableReturnsNull();

	/**
	 * @covers Opt_View::__set
	 * @covers Opt_View::__get
	 * @covers Opt_View::__isset
	 */
	public function testVariableExists()
	{
		$view = new Opt_View('template.tpl');
		$view->variable = 'Foo';
		$this->assertTrue(isset($view->variable));
		$this->assertFalse(isset($view->foo));
	} // end testVariableExists();

	/**
	 * @covers Opt_View::assign
	 * @covers Opt_View::get
	 */
	public function testAssignNormalMethods()
	{
		$view = new Opt_View('template.tpl');
		$view->assign('variable', 'Foo');
		$this->assertEquals('Foo', $view->get('variable'));
	} // end testAssignNormalMethods();

	/**
	 * @covers Opt_View::assignGlobal
	 * @covers Opt_View::getGlobal
	 */
	public function testAssignGlobalVars()
	{
		Opt_View::assignGlobal('variable', 'Foo');
		$this->assertEquals('Foo', Opt_View::getGlobal('variable'));
	} // end testAssignGlobalVars();

} // end Package_ViewTest;