<?php
	function makeTree()
	{
		$root = new Opt_Xml_Root;

		$node = new Opt_Xml_Text('Foo');
		$node->appendChild(new Opt_Xml_Expression('$foo'));
		$root->appendChild($node);

		$node->appendChild(new Opt_Xml_Element('foo'));

		return $root;
	} // end makeTree();

	require('./init.php');

	Opl_Debug::write('Creating tree 1.');
	$tree1 = makeTree();
	Opl_Debug::write('Now everything should be destroyed.');
	$tree1->dispose();
	unset($tree1);

	Opl_Debug::write('Creating tree 2.');
	$tree2 = makeTree();
	Opl_Debug::write('Now everything should be destroyed.');
	$tree2->dispose();
	unset($tree2);

