<?php

require('./init.php');

class Opt_Instruction_Test extends Opt_Compiler_Processor
{
	protected $_name = 'test';

	public function configure()
	{
		$this->_addInstructions('opt:supercomponent');
	} // end configure();

	public function processNode(Opt_Xml_Node $node)
	{
		$this->_compiler->setConversion('##component', 'componentFactory(\'%CLASS%\', \'%TAG%\', %ATTRIBUTES%)');
		$node->set('postprocess', true);
		$this->_process($node);
	} // end processNode();

	public function postprocessNode(Opt_Xml_Node $node)
	{
		$this->_compiler->unsetConversion('##component');
	} // end postprocessNode();
} // end Opt_Instruction_Test;

function componentFactory($class, $tag, $attributes)
{
	echo '<p>Creating '.$class.' (tag: '.$tag.')</p>';
	var_dump($attributes);
	return new selectComponent;
} // end componentFactory();
	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		require('./components.php');
		$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:select', 'selectComponent');
		$tpl->register(Opt_Class::OPT_INSTRUCTION, 'Test');
		$tpl->setup();
		$hello = (isset($_POST['hello']) ? $_POST['hello'] : 0);

		$view = new Opt_View('test_supercomponents.tpl');

		$view->assign('list', array(0 =>
			'Position 1',
			'Position 2',
			'Position 3',
			'Position 4',
			'Position 5',
			'Position 6',
			'Position 7',
			'Position 8',
			'Position 9'
		));
		$view->assign('selected', $hello);
		$view->assign('valid', true);
		if($hello == 4)
		{
			$view->assign('valid', false);
		}
		$out = new Opt_Output_Http;
		$out->render($view);
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>