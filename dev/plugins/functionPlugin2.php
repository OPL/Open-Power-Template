<?php
	function Opt_FunctionPlugin_2()
	{
		return 'I am a plugin 2';
	} // end Opt_FunctionPlugin_2();
	
	$this->register(Opt_Class::PHP_FUNCTION, 'functionPlugin2', 'Opt_FunctionPlugin_2');
?>