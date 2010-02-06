<?php
/**
 * The tests for Opt_Cdf_Manager
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers Opt_Cdf_Manager
 */
class Package_Cdf_LoaderTest extends Extra_Testcase
{
	const PATH = './Package/Cdf/TestFiles/';

	/**
	 * The CDF manager.
	 * @var Opt_Cdf_Manager
	 */
	private $_manager;

	/**
	 * The tested loader.
	 * @var Opt_Cdf_Loader
	 */
	private $_loader;

	/**
	 * Sets up the object to test. We use Opt_Xml_Element to test
	 * the basic interface.
	 */
	public function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->_manager = new Opt_Cdf_Manager('class', array(
			'Array' => 'Opt_Format_Array',
			'Objective' => 'Opt_Format_Objective'
		));

		$this->_loader = new Opt_Cdf_Loader($this->_manager);
		// @codeCoverageIgnoreStop
	} // end setUp();

	/**
	 * Removes the tested object.
	 */
	public function tearDown()
	{
		// @codeCoverageIgnoreStart
		if($this->_manager !== null)
		{
			$this->_manager = null;
		}
		// @codeCoverageIgnoreStop
	} // end tearDown();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testSimpleLoading()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'simple_loading.cdf');
		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('foo', 'bar', 'generic', $locator)));
	} // end testSimpleLoading();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testMultipleFormats()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'multiple_formats.cdf');
		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('foo', 'bar', 'generic', $locator)));
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('foo', 'bar', 'testing', $locator)));
	} // end testMultipleFormats();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testNestedElements()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array('bar#joe')));

		$this->_loader->load(self::PATH.'nested_elements.cdf');
		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('foo', 'bar', 'generic', $locator)));
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('foo', 'bar', 'testing', $locator)));
	} // end testNestedElements();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testMultipleDefs()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(4))
			->method('getElementLocation')
			->will($this->returnValue(array('bar#joe')));

		$this->_loader->load(self::PATH.'multiple_defs.cdf');
		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('foo', 'bar', 'generic', $locator)));
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('foo', 'bar', 'testing', $locator)));

		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('joe', null, 'generic', $locator)));
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('joe', null, 'testing', $locator)));
	} // end testMultipleDefs();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testDecoration()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'decoration.cdf');

		$format = $this->_manager->getFormat('foo', 'bar', 'generic', $locator);
		$this->assertEquals('Opt_Format_Array', get_class($format));
		$this->assertTrue($format->isDecorating());
	} // end testDecoration();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testVariables()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'variables.cdf');
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('variable', 'item.subitem', 'generic', $locator)));
	} // end testVariables();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testSlashedType()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'slashed_type.cdf');
		$this->assertEquals('Opt_Format_Array', get_class($this->_manager->getFormat('foo', 'bar', 'data-format', $locator)));
	} // end testSlashedType();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 * @expectedException Opt_NoMatchingFormat_Exception
	 */
	public function testMultilineComments1()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'comments_multiline.cdf');
		$this->_manager->getFormat('foo', 'bar', 'generic', $locator);
	} // end testMultilineComments1();

	/**
	 * @covers Opt_Cdf_Lexer
	 * @covers Opt_Cdf_Parser
	 * @covers Opt_Cdf_Loader::load
	 * @covers Opt_Cdf_Loader::_addDefinition
	 */
	public function testMultilineComments2()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_loader->load(self::PATH.'comments_multiline.cdf');
		$this->assertEquals('Opt_Format_Objective', get_class($this->_manager->getFormat('bar', 'joe', 'generic', $locator)));
	} // end testMultilineComments2();

} // end Package_Cdf_LoaderTest;