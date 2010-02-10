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
class Package_Cdf_ManagerTest extends Extra_Testcase
{
	/**
	 * The tested object.
	 * @var Opt_Cdf_Manager
	 */
	private $_obj;

	/**
	 * Sets up the object to test. We use Opt_Xml_Element to test
	 * the basic interface.
	 */
	public function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->_obj = new Opt_Cdf_Manager('class', array(
			'Array' => 'Opt_Format_Array',
			'Objective' => 'Opt_Format_Objective'
		));
		// @codeCoverageIgnoreStop
	} // end setUp();

	/**
	 * Removes the tested object.
	 */
	public function tearDown()
	{
		// @codeCoverageIgnoreStart
		if($this->_obj !== null)
		{
			$this->_obj = null;
		}
		// @codeCoverageIgnoreStop
	} // end tearDown();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreation()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreation();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreationElementOnly()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', null, 'Array', array());
		$format = $this->_obj->getFormat('foo', null, $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreationElementOnly();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatCreationIdOnly()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat(null, 'bar', 'Array', array());
		$format = $this->_obj->getFormat(null, 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreationIdOnly();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFullyQualifiedPathThatMatches()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('goo', 'hoo')));

		$this->_obj->addFormat('foo', 'bar', 'Array', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathThatMatches();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFullyQualifiedPathThatDoesNotMatch()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'Objective', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathThatMatches();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFullyQualifiedPathWithId()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('goo#identifier', 'hoo')));

		$this->_obj->addFormat('foo', 'bar', 'Array', array('goo#identifier', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathWithId();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testMultiplePathsSelection()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testMultiplePathsSelection();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFormatCaching()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', $locator);
		$formatB = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
		$this->assertSame($formatA, $formatB);
	} // end testFormatCaching();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFirstlySelectTypeAndId()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', null, 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFirstlySelectTypeAndId();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testSecondlySelectId()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', null, 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'Array', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testSecondlySelectId();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFinallySelectType()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', null, 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'joe', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFinallySelectType();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testIfNoIdSelectType()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', null, 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', null, $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testIfNoIdSelectType();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testUsingDefaultLocator()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->setLocator($locator);

		$this->_obj->addFormat('foo', null, 'Array', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', null);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testUsingDefaultLocator();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @expectedException Opt_NoMatchingFormat_Exception
	 */
	public function testThrowsExceptionIfNoMatchingDefinition()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'joe', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', $locator);
	} // end testThrowsExceptionIfNoMatchingDefinition();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFormatDecoration()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', null, 'Array/Objective', array());
		$format = $this->_obj->getFormat('foo', null, $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
		$this->assertTrue($format->isDecorating());
	} // end testFormatDecoration();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @expectedException Opt_FormatNotFound_Exception
	 */
	public function testFormatDecorationMissingFormat()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', null, 'Array/MissingFormat', array());
		$this->_obj->getFormat('foo', null, $locator);
	} // end testFormatDecorationMissingFormat();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @covers Opt_Cdf_Manager::setLocality
	 */
	public function testSelectingGlobalDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->setLocality(Opt_Cdf_Manager::AS_GLOBAL);
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testSelectingGlobalDefinitions();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @covers Opt_Cdf_Manager::setLocality
	 */
	public function testSelectingLocalDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->setLocality(Opt_Cdf_Manager::AS_LOCAL);
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testSelectingLocalDefinitions();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @covers Opt_Cdf_Manager::setLocality
	 * @covers Opt_Cdf_Manager::setLocals
	 */
	public function testSelectingLocalCdfDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->setLocality('file.cdf');
		$this->_obj->setLocals(array('file.cdf'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testSelectingLocalCdfDefinitions();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @covers Opt_Cdf_Manager::setLocality
	 * @covers Opt_Cdf_Manager::setLocals
	 * @expectedException Opt_NoMatchingFormat_Exception
	 */
	public function testMaskingLocalCdfDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->setLocality('file.cdf');
		$this->_obj->setLocals(array('foo.cdf'));
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$this->_obj->getFormat('foo', 'bar', $locator);
	} // end testMaskingLocalCdfDefinitions();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 * @covers Opt_Cdf_Manager::setLocality
	 * @covers Opt_Cdf_Manager::clearLocals
	 * @expectedException Opt_NoMatchingFormat_Exception
	 */
	public function testDisablingLocalDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->setLocality(Opt_Cdf_Manager::AS_LOCAL);
		$this->_obj->addFormat('foo', 'bar', 'Array', array());
		$this->_obj->addFormat('joe', 'goo', 'Array', array());
		$this->assertTrue($this->_obj->getFormat('foo', 'bar', $locator) instanceof Opt_Format_Array);

		$this->_obj->clearLocals();

		$this->_obj->getFormat('joe', 'goo', $locator);
	} // end testDisablingLocalDefinitions();
} // end Package_Cdf_ManagerTest;