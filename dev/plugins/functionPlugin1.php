<?php
	function Opt_FunctionPlugin_1()
	{
		return 'I am a plugin 1';
	} // end Opt_FunctionPlugin_1();
	
	$this->register(Opt_Class::PHP_FUNCTION, 'functionPlugin1', 'Opt_FunctionPlugin_1');
?>