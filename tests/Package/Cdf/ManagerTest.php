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

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', null, 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Compiler_Format);
	} // end testTheSimplestFormatCreationElementOnly();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testTheSimplestFormatTwoDefinitions()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array()));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$this->_obj->addFormat('foo', 'bar', 'testing', 'Objective', array());

		$this->assertEquals('Opt_Format_Array', get_class($this->_obj->getFormat('foo', 'bar', 'generic', $locator)));
		$this->assertEquals('Opt_Format_Objective', get_class($this->_obj->getFormat('foo', 'bar', 'testing', $locator)));
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

		$this->_obj->addFormat(null, 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat(null, 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testFullyQualifiedPathThatMatches();

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

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);
		$formatB = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
		$this->assertSame($formatA, $formatB);
	} // end testFormatCaching();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testAccessingAnotherFormatType()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'testing', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
	} // end testAccessingAnotherFormatType();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testAccessingAnotherFormatTypeOrderChangeDoesntMatter()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'testing', 'Array', array('rotfl', 'lmao'));
		$formatA = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
	} // end testAccessingAnotherFormatTypeOrderChangeDoesntMatter();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testAccessingAnotherFormatTypeComplexMixingDoesntMatter()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'testing', 'Objective', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'mysterious', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'testing', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$this->_obj->addFormat('foo', 'bar', 'mysterious', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
	} // end testAccessingAnotherFormatTypeComplexMixingDoesntMatter();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testAccessingAnotherFormatTypeWithNoDefinition()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testAccessingAnotherFormatTypeWithNoDefinition();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testAccessingAnotherFormatTypeMissingSelectsGeneric()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->once())
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'testing', 'Objective', array('goo', 'hoo'));
		$this->_obj->addFormat('foo', 'moe', 'generic', 'Objective', array('goo', 'hoo', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'mysterious', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'moe', 'testing', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$this->_obj->addFormat('foo', 'bar', 'mysterious', 'Objective', array());
		$format = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($format instanceof Opt_Format_Array);
	} // end testAccessingAnotherFormatTypeMissingSelectsGeneric();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFormatCachingIfGenericSelected()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'hoo', 'woo'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('goo', 'rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array());
		$formatA = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);
		$formatB = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Array);
		$this->assertSame($formatA, $formatB);
	} // end testFormatCachingIfGenericSelected();

	/**
	 * @covers Opt_Cdf_Manager::getFormat
	 * @covers Opt_Cdf_Manager::addFormat
	 */
	public function testFormatCachingButForYetAnotherType()
	{
		$locator = $this->getMock('Opt_Cdf_Locator_Interface', array('getElementLocation'));
		$locator->expects($this->exactly(2))
			->method('getElementLocation')
			->will($this->returnValue(array('rotfl', 'lmao')));

		$this->_obj->addFormat('foo', 'bar', 'mysterious', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'testing', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$formatA = $this->_obj->getFormat('foo', 'bar', 'mysterious', $locator);
		$formatB = $this->_obj->getFormat('foo', 'bar', 'testing', $locator);

		$this->assertTrue($formatA instanceof Opt_Format_Objective);
		$this->assertTrue($formatB instanceof Opt_Format_Array);
	} // end testFormatCachingButForYetAnotherType();

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

		$this->_obj->addFormat('foo', null, 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'generic', 'Array', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'joe', 'generic', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Array', array('rotfl', 'lmao'));
		$this->_obj->addFormat(null, 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$this->_obj->addFormat('foo', 'bar', 'generic', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', null, 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Array', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', null, 'generic');

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

		$this->_obj->addFormat('foo', 'joe', 'generic', 'Objective', array('rotfl', 'lmao'));
		$format = $this->_obj->getFormat('foo', 'bar', 'generic', $locator);
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

		$this->_obj->addFormat('foo', null, 'generic', 'Array/Objective', array());
		$format = $this->_obj->getFormat('foo', null, 'generic', $locator);

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

		$this->_obj->addFormat('foo', null, 'generic', 'Array/MissingFormat', array());
		$this->_obj->getFormat('foo', null, 'generic', $locator);
	} // end testFormatDecorationMissingFormat();
} // end Package_Cdf_ManagerTest;